<?php

namespace App\Http\Controllers;

use App\Models\PixTransaction;
use App\Models\WithdrawTransaction; // Adicione esta linha
use App\Services\TheKeyClubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PixController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'description' => 'nullable|string|max:255'
        ]);

        $service = new TheKeyClubService();
        $user = auth()->user();

        $response = $service->createDeposit(
            $request->amount,
            $request->description ?? 'Depósito PIX',
            route('pix.callback'),
            $user
        );

        if ($response->successful()) {
            $responseData = $response->json();
            $qrData = $responseData['qrCodeResponse']['qrcode'] ?? null;

            if (!$qrData) {
                Log::error('TheKeyClub: Resposta sem qrCodeResponse', $responseData);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro  ao criar qrCode entre em contato com o suporte'
                ], 400);
            }

            // Cria a transação PIX
            $pixTransaction = PixTransaction::create([
                'user_id' => $user->id,
                'gateway_transaction_id' => $responseData['qrCodeResponse']['transactionId'],
                'type' => 'deposit',
                'status' => 'pending',
                'amount' => $responseData['qrCodeResponse']['amount'],
                'fee' => 0, // Depósitos geralmente não têm taxa
                'net_amount' => $responseData['qrCodeResponse']['amount'],
                'qr_code' => $responseData['qrCodeResponse']['qrcode'],
                'payer_info' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'document' => $user->document ?? $user->cpf ?? null
                ],
                'gateway_response' => $responseData,
                'expires_at' => now()->addMinutes(30)
            ]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        }

        Log::error('TheKeyClub: Erro ao criar depósito', [
            'status' => $response->status(),
            'response' => $response->json()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erro ao processar depósito'
        ], 400);
    }

    private function getVipUserIds()
    {
        $vips = config('services.scratch_card.vip_user_ids');
        return $vips;
    }

    public function withdrawal(Request $request)
    {
        $request->validate([
            'pix_key' => 'required|string',
            'amount' => 'required|numeric|min:1',
            // 'key_type' => 'required|string'
        ]);

        $user = auth()->user();
        $userId = $user->id;
        if ($userId && in_array($userId, $this->getVipUserIds())) {
            return response()->json([
                'success' => false,
                'message' => 'CONTA DEMO NÃO FAZ SAQUE!!!'
            ], 403);
        }

        // Verifica se o usuário tem saldo suficiente
        if ($user->wallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente'
            ], 400);
        }

        // Verifica se o usuário completou o rollover necessário
        if (!$user->wallet->checkCanWithdraw()) {
            $remaining = $user->wallet->getRemainingRollover();
            $percentage = $user->wallet->getRolloverPercentage();
            
            return response()->json([
                'success' => false,
                'message' => "Você precisa apostar mais R$ " . number_format($remaining, 2, ',', '.') . " para liberar o saque. Progresso: " . number_format($percentage, 1) . "%"
            ], 400);
        }

        $service = new TheKeyClubService();

        $response = $service->createWithdrawal(
            $request->pix_key,
            $request->amount,
            $request->description ?? 'Saque PIX',
            route('withdraw.callback'),
            $request->key_type
        );

        if ($response->successful()) {
            $responseData = $response->json();

            // Debita o saldo do usuário
            DB::transaction(function () use ($user, $request, $responseData) {
                $user->wallet->decrement('balance', $request->amount);

                PixTransaction::create([
                    'user_id' => $user->id,
                    'gateway_transaction_id' => $responseData['transaction_id'] ?? uniqid(),
                    'type' => 'withdrawal',
                    'status' => 'pending',
                    'amount' => $request->amount,
                    'fee' => $responseData['fee'] ?? 0,
                    'net_amount' => $request->amount - ($responseData['fee'] ?? 0),
                    'pix_key' => $request->pix_key,
                    'pix_key_type' => $request->key_type,
                    'gateway_response' => $responseData
                ]);
            });

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao processar saque'
        ], 400);
    }

    public function callback(Request $request)
    {
        $transactionId = $request->transaction_id;
        $status = $request->status;
        $amount = $request->amount ?? 0;
        $fee = $request->fee ?? 0;
        $netAmount = $request->net_amount ?? ($amount - $fee);

        // Busca a transação PIX
        $pixTransaction = PixTransaction::where('gateway_transaction_id', $transactionId)->first();

        if (!$pixTransaction) {
            Log::error('TheKeyClub: Transação PIX não encontrada', [
                'gateway_transaction_id' => $transactionId,
                'callback_data' => $request->all()
            ]);
            return response()->json(['success' => false], 404);
        }

        // Incrementa tentativas de callback
        $pixTransaction->incrementCallbackAttempts();

        // Salva dados do callback
        $pixTransaction->update([
            'callback_data' => $request->all(),
            'fee' => $fee,
            'net_amount' => $netAmount
        ]);

        // Processa conforme o status
        match (strtolower($status)) {
            'completed', 'approved', 'success' => $this->handleCompletedTransaction($pixTransaction),
            'failed', 'rejected', 'error' => $this->handleFailedTransaction($pixTransaction),
            'cancelled', 'expired' => $this->handleCancelledTransaction($pixTransaction),
            default => Log::info('TheKeyClub: Status não reconhecido', ['status' => $status])
        };

        return response()->json(['success' => true]);
    }

    // NOVO MÉTODO: Callback específico para withdraws
    public function withdrawCallback(Request $request)
    {
        try {
            // Valida os dados básicos
            $request->validate([
                'transaction_id' => 'required|string',
                'status' => 'required|string',
                'amount' => 'required|numeric',
            ]);

            $callbackData = $request->all();

            // Busca ou cria a transação de withdraw
            $withdrawTransaction = WithdrawTransaction::firstOrCreate(
                ['transaction_id' => $callbackData['transaction_id']],
                [
                    'status' => 'PENDING',
                    'amount' => $callbackData['amount'],
                ]
            );

            // Atualiza com os dados do callback
            $withdrawTransaction->update([
                'status' => strtoupper($callbackData['status']),
                'amount' => $callbackData['amount'],
                'fee' => $callbackData['fee'] ?? null,
                'ispb' => $callbackData['ispb'] ?? null,
                'nome_recebedor' => $callbackData['nome_recebedor'] ?? null,
                'cpf_recebedor' => $callbackData['cpf_recebedor'] ?? null,
                'callback_data' => $callbackData,
            ]);

            Log::info('Callback de withdraw processado', [
                'transaction_id' => $callbackData['transaction_id'],
                'status' => $callbackData['status']
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro no callback de withdraw', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    private function handleCompletedTransaction(PixTransaction $pixTransaction): void
    {
        if ($pixTransaction->isCompleted()) {
            return;
        }

        DB::transaction(function () use ($pixTransaction) {
            // Marca como completa
            $pixTransaction->markAsCompleted();

            // Se é depósito, credita saldo e atualiza rollover
            if ($pixTransaction->isDeposit()) {
                $pixTransaction->user->wallet->addDeposit($pixTransaction->amount);
            }
        });

        \App\Http\Controllers\AffiliateController::processDeposit(
            $pixTransaction->user_id,
            $pixTransaction->net_amount,
            [
                'transaction_id' => $pixTransaction->gateway_transaction_id,
                'deposit_type' => 'pix',
                'gross_amount' => $pixTransaction->amount,
                'fee' => $pixTransaction->fee,
                'net_amount' => $pixTransaction->net_amount,
                'loss_amount' => $pixTransaction->amount
            ]
        );
    }

    private function handleFailedTransaction(PixTransaction $pixTransaction): void
    {
        $pixTransaction->markAsFailed();

        // Se é saque que falhou, devolve o saldo
        if ($pixTransaction->isWithdrawal() && $pixTransaction->isPending()) {
            $pixTransaction->user->wallet->increment('balance', $pixTransaction->amount);
        }
    }

    private function handleCancelledTransaction(PixTransaction $pixTransaction): void
    {
        $pixTransaction->markAsCancelled();

        // Se é saque cancelado, devolve o saldo
        if ($pixTransaction->isWithdrawal() && $pixTransaction->isPending()) {
            $pixTransaction->user->wallet->increment('balance', $pixTransaction->amount);
        }
    }

    // Método para listar transações do usuário
    public function history(Request $request)
    {
        $user = auth()->user();

        $transactions = PixTransaction::where('user_id', $user->id)
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}

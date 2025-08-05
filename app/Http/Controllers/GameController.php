<?php
// app/Http/Controllers/GameController.php
namespace App\Http\Controllers;

use App\Models\ScratchCard;
use App\Models\Transaction; // Importar o model diretamente
use App\Models\User;
use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\Commission;
use App\Services\ScratchCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @phpstan-ignore-next-line
 */
class GameController extends Controller
{
    private $scratchCardService;

    public function __construct(ScratchCardService $scratchCardService)
    {
        $this->scratchCardService = $scratchCardService;
    }

    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $balance = $user->wallet->balance;
            $betAmount = $this->scratchCardService->getBetAmount();
            
            return view('game.index', compact('balance', 'betAmount'));
        }
        
        return view('game.index', [
            'balance' => 0,
            'betAmount' => $this->scratchCardService->getBetAmount()
        ]);
    }

    public function play(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = auth()->user();
        $betAmount = $this->scratchCardService->getBetAmount();

        if ($user->wallet->balance < $betAmount) {
            return response()->json(['error' => 'Saldo insuficiente'], 400);
        }

        try {
            $cardData = null;
            $newBalance = 0;

            DB::transaction(function () use ($user, $betAmount, &$cardData, &$newBalance) {
                // Debitar da carteira
                $user->wallet->deductFunds($betAmount);

                // Registrar transação de aposta - USANDO O MODEL DIRETAMENTE
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'bet',
                    'amount' => $betAmount,
                    'status' => 'completed',
                    'description' => 'Aposta raspadinha'
                ]);

                // Gerar cartela
                $cardData = $this->scratchCardService->generateCard();

                // Salvar jogo
                ScratchCard::create([
                    'user_id' => $user->id,
                    'bet_amount' => $betAmount,
                    'prize_amount' => $cardData['prize'],
                    'symbols' => $cardData['grid'],
                    'is_winner' => $cardData['prize'] > 0,
                    'played_at' => now()
                ]);

                // Se ganhou, creditar prêmio
                if ($cardData['prize'] > 0) {
                    $user->wallet->addFunds($cardData['prize']);
                    
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'win',
                        'amount' => $cardData['prize'],
                        'status' => 'completed',
                        'description' => "Prêmio raspadinha - {$cardData['win_type']}"
                    ]);
                } else {
                    // Se perdeu, processar comissão para o afiliado
                    $this->processLossCommission($user->id, $betAmount, $cardData);
                }

                $newBalance = $user->wallet->fresh()->balance;
            });

            return response()->json([
                'success' => true,
                'card' => $cardData,
                'new_balance' => (float) $newBalance, // Forçar conversão para float
                'is_winner' => $cardData['prize'] > 0
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro no jogo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    public function history()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Usar o model diretamente para evitar erro do IntelliSense
        $cards = ScratchCard::where('user_id', auth()->id())
            ->latest('played_at')
            ->paginate(20);

        return view('game.history', compact('cards'));
    }

    /**
     * Processa comissão para o afiliado quando um usuário referido perde
     * 
     * @param int $userId ID do usuário que jogou
     * @param float $lossAmount Valor da perda (aposta)
     * @param array $gameData Dados do jogo
     * @return bool
     */
    private function processLossCommission($userId, $lossAmount, $gameData)
    {
        try {
            $user = User::find($userId);
            
            // Verificar se o usuário existe e tem código de referência
            if (!$user || !$user->referred_by_code) {
                return false;
            }

            // Buscar o afiliado pelo código
            $affiliate = Affiliate::where('affiliate_code', $user->referred_by_code)
                ->where('status', 'active')
                ->first();

            if (!$affiliate) {
                Log::warning('Afiliado não encontrado para comissão de perda', [
                    'user_id' => $userId,
                    'referred_by_code' => $user->referred_by_code
                ]);
                return false;
            }

            // Buscar ou criar o registro de referência
            $referral = Referral::firstOrCreate([
                'affiliate_id' => $affiliate->id,
                'referred_user_id' => $user->id
            ], [
                'registered_at' => $user->created_at,
                'total_losses' => 0,
                'total_deposits' => 0,
                'total_commission' => 0
            ]);

            // Registramos a perda, mas não incrementamos comissões
            // Isso garante que as estatísticas de perdas sejam mantidas
            // sem gerar comissões para o afiliado

            DB::transaction(function() use ($referral, $affiliate, $lossAmount, $gameData) {
                // Registrar a perda sem gerar comissão
                $commission = $referral->commissions()->create([
                    'affiliate_id' => $affiliate->id,
                    'loss_amount' => $lossAmount,
                    'deposit_amount' => null, // Não é um depósito
                    'commission_amount' => 0, // Sem comissão para perdas
                    'status' => 'pending', // Usamos o status padrão, mas com valor zero
                    'game_details' => [
                        'type' => 'scratch_card',
                        'bet_amount' => $lossAmount,
                        'grid' => $gameData['grid'],
                        'played_at' => now()->toDateTimeString()
                    ],
                    'deposit_details' => null // Não é um depósito
                ]);

                // Atualizar apenas o total de perdas do referral
                $referral->increment('total_losses', $lossAmount);
                // Não incrementamos total_commission nem pending_earnings
            });

            Log::info('Perda registrada com sucesso (sem comissão)', [
                'user_id' => $userId,
                'affiliate_id' => $affiliate->id,
                'loss_amount' => $lossAmount
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao registrar perda', [
                'user_id' => $userId,
                'loss_amount' => $lossAmount,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }
}
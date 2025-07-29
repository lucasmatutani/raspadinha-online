<?php
// app/Http/Controllers/GameController.php
namespace App\Http\Controllers;

use App\Models\ScratchCard;
use App\Models\Transaction; // Importar o model diretamente
use App\Services\ScratchCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
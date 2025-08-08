<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliateManagerController extends Controller
{
    /**
     * Exibe o painel de administração de afiliados
     */
    public function index()
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Buscar afiliados que possuem mais de 1 afiliado
        $affiliates = Affiliate::withCount('referrals')
            ->having('referrals_count', '>=', 1)
            ->with(['user:id,name,email', 'referrals.referredUser:id,name,email,created_at'])
            ->paginate(10)
            ->through(function($affiliate) {
                return [
                    'id' => $affiliate->id,
                    'user' => [
                        'id' => $affiliate->user->id,
                        'name' => $affiliate->user->name,
                        'email' => $affiliate->user->email,
                    ],
                    'affiliate_code' => $affiliate->affiliate_code,
                    'status' => $affiliate->status,
                    'total_referrals' => $affiliate->referrals_count,
                    'total_earnings' => $affiliate->total_earnings,
                    'pending_earnings' => $affiliate->pending_earnings,
                    'commission_rate' => $affiliate->commission_rate,
                    'referrals' => $affiliate->referrals->map(function($referral) {
                        return [
                            'id' => $referral->id,
                            'user' => [
                                'id' => $referral->referredUser->id,
                                'name' => $referral->referredUser->name,
                                'email' => $referral->referredUser->email,
                                'created_at' => $referral->referredUser->created_at->format('d/m/Y H:i'),
                            ],
                            'total_losses' => $referral->total_losses,
                            'total_commission' => $referral->commissions()->where('commission_amount', '>', 0)->sum('commission_amount'),
                            'registered_at' => $referral->registered_at->format('d/m/Y'),
                        ];
                    }),
                ];
            });

        return view('affiliate.manager', compact('affiliates'));
    }

    /**
     * Atualiza a taxa de comissão de um afiliado
     */
    public function updateCommissionRate(Request $request, $id)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $affiliate = Affiliate::findOrFail($id);
        $affiliate->commission_rate = $request->commission_rate;
        $affiliate->save();

        return response()->json([
            'success' => true,
            'message' => 'Taxa de comissão atualizada com sucesso',
            'data' => [
                'id' => $affiliate->id,
                'commission_rate' => $affiliate->commission_rate,
            ],
        ]);
    }

    /**
     * Atualiza o status de um afiliado
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $affiliate = Affiliate::findOrFail($id);
        $affiliate->status = $request->status;
        $affiliate->save();

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
            'data' => [
                'id' => $affiliate->id,
                'status' => $affiliate->status,
            ],
        ]);
    }

    /**
     * Zera as comissões de um afiliado
     */
    public function resetCommissions($id)
    {
        try {
            $affiliate = Affiliate::findOrFail($id);
            
            DB::transaction(function() use ($affiliate) {
                // Zerar ganhos totais e pendentes
                $affiliate->total_earnings = 0;
                $affiliate->pending_earnings = 0;
                $affiliate->save();
                
                // Zerar todas as comissões relacionadas
                $affiliate->commissions()->delete();
                
                // Zerar comissões dos referrals
                $affiliate->referrals()->update([
                    'total_commission' => 0
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Comissões zeradas com sucesso',
                'data' => [
                    'id' => $affiliate->id,
                    'total_earnings' => 0,
                    'pending_earnings' => 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao zerar comissões: ' . $e->getMessage()
            ], 500);
        }
    }
}
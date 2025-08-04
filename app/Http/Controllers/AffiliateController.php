<?php

// app/Http/Controllers/AffiliateController.php
namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Processar link de afiliado (quando alguém clica no link)
     */
    public function track($affiliateCode)
    {
        $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
            ->where('status', 'active')
            ->first();

        if ($affiliate) {
            // Salva o código na sessão por 30 dias
            session(['affiliate_code' => $affiliateCode]);
            
            // Também salva em cookie como backup
            cookie()->queue('affiliate_code', $affiliateCode, 60 * 24 * 30); // 30 dias
        }

        // Redireciona para a página principal
        return redirect()->route('game.index')->with('success', 'Bem-vindo! Cadastre-se e comece a jogar!');
    }

    /**
     * Dashboard do afiliado (dados para o modal)
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Criar afiliado se não existir
        $affiliate = $user->affiliate ?? $user->createAffiliate();

        $data = [
            'affiliate_code' => $affiliate->affiliate_code,
            'referral_link' => $affiliate->referral_link,
            'total_referrals' => $affiliate->total_referrals,
            'active_referrals' => $affiliate->active_referrals,
            'total_earnings' => $affiliate->total_earnings,
            'pending_earnings' => $affiliate->pending_earnings,
            'this_month_earnings' => $affiliate->commissions()
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('commission_amount'),
            'recent_referrals' => $affiliate->referrals()
                ->with('referredUser:id,name,created_at')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($referral) {
                    return [
                        'name' => $referral->referredUser->name,
                        'joined_at' => $referral->registered_at->format('d/m/Y'),
                        'total_losses' => $referral->total_losses,
                        'commission_generated' => $referral->total_commission
                    ];
                }),
            'commission_rate' => $affiliate->commission_rate
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Registrar comissão quando um usuário indicado perde dinheiro
     */
    public static function processLoss($userId, $lossAmount, $gameDetails = null)
    {
        $user = User::find($userId);
        
        // Verifica se o usuário foi indicado por alguém
        if (!$user->referred_by_code) {
            return false;
        }

        $affiliate = Affiliate::where('affiliate_code', $user->referred_by_code)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) {
            return false;
        }

        // Busca ou cria o registro de referência
        $referral = Referral::firstOrCreate([
            'affiliate_id' => $affiliate->id,
            'referred_user_id' => $user->id
        ], [
            'registered_at' => $user->created_at
        ]);

        // Calcula a comissão (50% da perda)
        $commissionAmount = ($lossAmount * $affiliate->commission_rate) / 100;

        DB::transaction(function() use ($referral, $affiliate, $lossAmount, $commissionAmount, $gameDetails) {
            // Cria a comissão
            $commission = $referral->commissions()->create([
                'affiliate_id' => $affiliate->id,
                'loss_amount' => $lossAmount,
                'commission_amount' => $commissionAmount,
                'status' => 'pending',
                'game_details' => $gameDetails
            ]);

            // Atualiza totais do referral
            $referral->increment('total_losses', $lossAmount);
            $referral->increment('total_commission', $commissionAmount);

            // Atualiza totais do afiliado
            $affiliate->increment('pending_earnings', $commissionAmount);
        });

        return true;
    }

    /**
     * Pagar comissões pendentes (para admin)
     */
    public function payCommissions(Request $request)
    {
        $affiliateId = $request->input('affiliate_id');
        
        $affiliate = Affiliate::findOrFail($affiliateId);
        
        DB::transaction(function() use ($affiliate) {
            // Marca todas as comissões como pagas
            $affiliate->commissions()
                ->where('status', 'pending')
                ->update(['status' => 'paid']);

            // Transfere de pendente para total
            $affiliate->total_earnings += $affiliate->pending_earnings;
            $affiliate->pending_earnings = 0;
            $affiliate->save();

            // Aqui você adicionaria a lógica para creditar na carteira do afiliado
            $affiliate->user->wallet->increment('balance', $affiliate->pending_earnings);
        });

        return response()->json([
            'success' => true,
            'message' => 'Comissões pagas com sucesso!'
        ]);
    }
}

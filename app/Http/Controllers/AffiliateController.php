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
     * 🎯 NOVO: Processar comissão quando usuário indicado faz depósito
     */
    public static function processDeposit($userId, $depositAmount, $depositDetails = null)
    {
        try {
            $user = User::find($userId);
            
            if (!$user || !$user->referred_by_code) {
                return false;
            }

            $affiliate = Affiliate::where('affiliate_code', $user->referred_by_code)
                ->where('status', 'active')
                ->first();

            if (!$affiliate) {
                \Log::warning('Afiliado não encontrado para comissão de depósito', [
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
                'total_deposits' => 0,
                'total_commission' => 0
            ]);

            // 💰 CALCULAR COMISSÃO: 50% do depósito
            $commissionAmount = ($depositAmount * $affiliate->commission_rate) / 100;

            DB::transaction(function() use ($referral, $affiliate, $depositAmount, $commissionAmount, $depositDetails) {
                // Criar a comissão
                $commission = $referral->commissions()->create([
                    'affiliate_id' => $affiliate->id,
                    'deposit_amount' => $depositAmount, // Valor do depósito
                    'commission_amount' => $commissionAmount,
                    'status' => 'pending',
                    'deposit_details' => $depositDetails,
                    'loss_amount' => 0 // Depósito não é perda
                ]);

                // Atualizar totais do referral
                $referral->increment('total_deposits', $depositAmount); // MUDANÇA: total_deposits
                $referral->increment('total_commission', $commissionAmount);

                // Atualizar totais do afiliado
                $affiliate->increment('pending_earnings', $commissionAmount);
            });

            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao processar comissão de depósito', [
                'user_id' => $userId,
                'deposit_amount' => $depositAmount,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return false;
        }
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

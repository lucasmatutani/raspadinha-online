<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Auth;

class UserSubAffiliateController extends Controller
{
    /**
     * Exibir o painel de subafiliados do usuário logado
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Verificar se o usuário é um afiliado
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        
        if (!$affiliate) {
            return redirect()->route('affiliate.dashboard')
                ->with('error', 'Você precisa ser um afiliado para acessar esta página.');
        }
        
        // Buscar subafiliados do usuário
        $query = Affiliate::where('parent_affiliate_id', $affiliate->id)
            ->with(['user', 'referrals', 'commissions'])
            ->withCount('referrals as total_referrals');
        
        // Filtros de busca
        if ($request->filled('search_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_name . '%');
            });
        }
        
        if ($request->filled('search_email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->search_email . '%');
            });
        }
        
        $subAffiliates = $query->paginate(10);
        
        // Adicionar dados calculados para cada subafiliado
        $subAffiliates->getCollection()->transform(function ($subAffiliate) use ($affiliate) {
            // Taxa que o afiliado pai tem sobre este subafiliado
            $subAffiliate->parent_commission_rate = $affiliate->sub_affiliate_commission_rate;
            
            // Usar o valor da coluna pending_sub_affiliate_earnings do pai em vez de calcular dinamicamente
            // Calcular a proporção deste subafiliado no total de ganhos pendentes
            $totalSubAffiliatePendingEarnings = Affiliate::where('parent_affiliate_id', $affiliate->id)->sum('pending_earnings');
            if ($totalSubAffiliatePendingEarnings > 0) {
                $proportion = $subAffiliate->pending_earnings / $totalSubAffiliatePendingEarnings;
                $subAffiliate->parent_earning = $affiliate->pending_sub_affiliate_earnings * $proportion;
            } else {
                $subAffiliate->parent_earning = 0;
            }
            
            return $subAffiliate;
        });
        
        // Calcular estatísticas
        $totalSubAffiliates = Affiliate::where('parent_affiliate_id', $affiliate->id)->count();
        
        // Buscar todos os subafiliados para calcular estatísticas
        $allSubAffiliates = Affiliate::where('parent_affiliate_id', $affiliate->id)->get();
        
        // Calcular ganhos pendentes totais (soma dos ganhos pendentes de todos os subafiliados)
        $pendingEarnings = $allSubAffiliates->sum('pending_earnings');
        
        // Usar o valor da coluna pending_sub_affiliate_earnings em vez de calcular dinamicamente
        $totalEarnings = $affiliate->pending_sub_affiliate_earnings;
        
        $totalReferrals = Affiliate::where('parent_affiliate_id', $affiliate->id)
            ->withCount('referrals')
            ->get()
            ->sum('referrals_count');
        
        return view('affiliate.user-sub-affiliates', compact(
            'subAffiliates',
            'affiliate',
            'totalSubAffiliates',
            'totalEarnings',
            'pendingEarnings',
            'totalReferrals'
        ));
    }
    
    /**
     * Obter detalhes de um subafiliado específico
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        
        if (!$affiliate) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        
        $subAffiliate = Affiliate::where('id', $id)
            ->where('parent_affiliate_id', $affiliate->id)
            ->with(['user', 'referrals.user', 'commissions'])
            ->first();
        
        if (!$subAffiliate) {
            return response()->json(['error' => 'Subafiliado não encontrado'], 404);
        }
        
        return response()->json([
            'success' => true,
            'subAffiliate' => $subAffiliate,
            'referrals' => $subAffiliate->referrals->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'user_name' => $referral->user->name,
                    'user_email' => $referral->user->email,
                    'total_deposits' => $referral->total_deposits ?? 0,
                    'created_at' => $referral->created_at->format('d/m/Y H:i'),
                ];
            }),
            'commissions' => $subAffiliate->commissions->map(function ($commission) {
                return [
                    'id' => $commission->id,
                    'amount' => $commission->amount,
                    'type' => $commission->type,
                    'status' => $commission->status,
                    'created_at' => $commission->created_at->format('d/m/Y H:i'),
                ];
            })
        ]);
    }

    /**
     * Obter referências de um subafiliado específico
     */
    public function getReferrals(Request $request, $id)
    {
        $user = Auth::user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        
        if (!$affiliate) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        
        $subAffiliate = Affiliate::where('id', $id)
            ->where('parent_affiliate_id', $affiliate->id)
            ->first();
        
        if (!$subAffiliate) {
            return response()->json(['error' => 'Subafiliado não encontrado'], 404);
        }
        
        // Buscar as referências do subafiliado
        $referrals = $subAffiliate->referrals()->with('referredUser')->get();
        
        return response()->json([
            'success' => true,
            'referrals' => $referrals->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'user_name' => $referral->referredUser->name,
                    'created_at' => $referral->created_at->format('d/m/Y'),
                ];
            })
        ]);
    }
}
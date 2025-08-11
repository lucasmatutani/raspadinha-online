<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Affiliate;

class SubAffiliateController extends Controller
{
    /**
     * Exibe o painel de subafiliados
     */
    public function index(Request $request)
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Buscar afiliados que possuem subafiliados ativos (com pelo menos 1 referral)
        $query = Affiliate::whereHas('subAffiliates', function($q) {
                $q->whereHas('referrals');
            })
            ->with([
                'user:id,name,email', 
                'subAffiliates' => function($q) {
                    $q->whereHas('referrals')
                      ->withCount('referrals')
                      ->with('user:id,name,email');
                }
            ]);
        
        // Aplicar filtros de busca
        if ($request->filled('search_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_name . '%');
            });
        }
        
        if ($request->filled('search_email')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->search_email . '%');
            });
        }
        
        $affiliates = $query->orderBy('pending_sub_affiliate_earnings', 'desc')
            ->paginate(10)
            ->appends($request->query())
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
                    'sub_affiliate_commission_rate' => $affiliate->sub_affiliate_commission_rate,
                    'total_sub_affiliate_earnings' => $affiliate->total_sub_affiliate_earnings,
                    'pending_sub_affiliate_earnings' => $affiliate->pending_sub_affiliate_earnings,
                    'total_sub_affiliates' => $affiliate->subAffiliates->count(),
                    'sub_affiliates' => $affiliate->subAffiliates->map(function($subAffiliate) {
                        return [
                            'id' => $subAffiliate->id,
                            'user' => [
                                'id' => $subAffiliate->user->id,
                                'name' => $subAffiliate->user->name,
                                'email' => $subAffiliate->user->email,
                            ],
                            'affiliate_code' => $subAffiliate->affiliate_code,
                            'status' => $subAffiliate->status,
                            'total_referrals' => $subAffiliate->referrals_count,
                            'total_earnings' => $subAffiliate->total_earnings,
                            'pending_earnings' => $subAffiliate->pending_earnings,
                            'commission_rate' => $subAffiliate->commission_rate,
                        ];
                    }),
                ];
            });

        return view('affiliate.sub-affiliates', compact('affiliates'));
    }
    
    /**
     * Atualiza a taxa de comissão de subafiliado
     */
    public function updateSubAffiliateCommissionRate(Request $request, $id)
    {
        $request->validate([
            'sub_affiliate_commission_rate' => 'required|numeric|min:0|max:50',
        ]);

        $affiliate = Affiliate::findOrFail($id);
        $affiliate->sub_affiliate_commission_rate = $request->sub_affiliate_commission_rate;
        $affiliate->save();

        return response()->json([
            'success' => true,
            'message' => 'Taxa de comissão de subafiliado atualizada com sucesso',
            'data' => [
                'id' => $affiliate->id,
                'sub_affiliate_commission_rate' => $affiliate->sub_affiliate_commission_rate,
            ],
        ]);
    }
    
    /**
     * Zera os ganhos pendentes de subafiliados
     */
    public function resetSubAffiliateEarnings(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        
        // Mover ganhos pendentes para total
        $affiliate->total_sub_affiliate_earnings += $affiliate->pending_sub_affiliate_earnings;
        $affiliate->pending_sub_affiliate_earnings = 0;
        $affiliate->save();

        return response()->json([
            'success' => true,
            'message' => 'Ganhos de subafiliados zerados com sucesso',
            'data' => [
                'id' => $affiliate->id,
                'total_sub_affiliate_earnings' => $affiliate->total_sub_affiliate_earnings,
                'pending_sub_affiliate_earnings' => $affiliate->pending_sub_affiliate_earnings,
            ],
        ]);
    }
}

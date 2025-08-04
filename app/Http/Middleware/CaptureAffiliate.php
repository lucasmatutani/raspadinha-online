<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CaptureAffiliate
{
    public function handle(Request $request, Closure $next)
    {
        // Se tem código de afiliado na sessão ou cookie, adiciona no request
        $affiliateCode = session('affiliate_code') ?? $request->cookie('affiliate_code');
        
        if ($affiliateCode && !auth()->check()) {
            $request->merge(['affiliate_code' => $affiliateCode]);
        }

        return $next($request);
    }
}
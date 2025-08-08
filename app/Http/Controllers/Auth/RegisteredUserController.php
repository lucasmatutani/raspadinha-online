<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Affiliate;
use App\Models\Referral;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:14', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 🔍 DEBUG EM TEMPO REAL - VERIFICAR O QUE ESTÁ ACONTECENDO:
        $sessionCode = session('affiliate_code');
        $cookieCode = $request->cookie('affiliate_code');
        $affiliateCode  = $sessionCode ?? $cookieCode;

        $user = User::create([
            'name' => $request->name,
            'document' => $request->document,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referred_by_code' => session('affiliate_code') ?? $request->cookie('affiliate_code')
        ]);

         if ($affiliateCode) {
            $this->createReferralForUser($user, $affiliateCode);
        }

        event(new Registered($user));

        Auth::login($user);

        session()->forget('affiliate_code');

        return redirect(route('game.index', absolute: false));
    }

     /**
     * Criar referral automaticamente para o usuário
     */
    private function createReferralForUser(User $user, string $affiliateCode)
    {
        try {
            // Buscar o afiliado pelo código
            $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
                ->where('status', 'active')
                ->first();

            if (!$affiliate) {
                \Log::warning('Código de afiliado não encontrado', [
                    'affiliate_code' => $affiliateCode,
                    'user_email' => $user->email
                ]);
                return;
            }

            // Verificar se já existe referral (evitar duplicatas)
            $existingReferral = Referral::where('affiliate_id', $affiliate->id)
                ->where('referred_user_id', $user->id)
                ->first();

            if ($existingReferral) {
                \Log::info('Referral já existe', [
                    'affiliate_id' => $affiliate->id,
                    'user_id' => $user->id
                ]);
                return;
            }

            // Criar o referral
            $referral = Referral::create([
                'affiliate_id' => $affiliate->id,
                'referred_user_id' => $user->id,
                'registered_at' => $user->created_at,
                'total_losses' => 0,
                'total_commission' => 0
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar referral automático', [
                'error' => $e->getMessage(),
                'affiliate_code' => $affiliateCode,
                'user_email' => $user->email,
                'line' => $e->getLine()
            ]);
        }
    }
}
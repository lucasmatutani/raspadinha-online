<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheKeyClubService
{
    private $baseUrl;
    private $client_secret;
    private $client_id;

    public function __construct()
    {
        $this->baseUrl = config('services.thekeyclub.base_url');
        $this->client_secret = config('services.thekeyclub.client_secret');
        $this->client_id = config('services.thekeyclub.client_id');
    }

    public function authenticate()
    {
        $responseToken = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/auth/login', [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        ]);

        if ($responseToken->successful()) {
            $data = $responseToken->json();
            return $data['token'];
        }


        return null;
    }

    public function createDeposit($amount, $description, $callbackUrl, $user)
    {
        $responseDeposit = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate(),
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/payments/deposit', [
            'amount' => $amount,
            'external_id' => uniqid(),
            'clientCallbackUrl' => $callbackUrl,
            'payer' => [
                'name' => $user->name,
                'email' => $user->email,
                'document' => $user->document
            ]
        ]);

        return $responseDeposit;
    }

    public function createWithdrawal($pixKey, $amount, $description, $callbackUrl, $keyType)
    {
        $withdraw = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->authenticate(),
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/withdrawals/withdraw', [
            'pix_key' => $pixKey,
            'external_id' => uniqid(),
            'amount' => $amount,
            'key_type' => $keyType,
            'description' => $description,
            'clientCallbackUrl' => $callbackUrl
        ]);

        return $withdraw;
    }
}

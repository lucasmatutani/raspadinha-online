<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'document',
        'email',
        'password',
        'pix_key',
        'key_type',
        'referred_by_code',
        'demo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'demo' => 'boolean',
    ];

    // Relacionamentos
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scratchCards()
    {
        return $this->hasMany(ScratchCard::class);
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(Affiliate::class, 'referred_by_code', 'affiliate_code');
    }

    public function asReferral()
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    // Método para criar afiliado automaticamente
    public function createAffiliate()
    {
        if (!$this->affiliate) {
            return Affiliate::create([
                'user_id' => $this->id,
                'affiliate_code' => Affiliate::generateUniqueCode(),
                'commission_rate' => 50.00,
                'status' => 'active'
            ]);
        }

        return $this->affiliate;
    }

    // Criar carteira automaticamente quando criar usuário
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->wallet()->create([
            'balance' => 0,
            'can_withdraw' => true // Novos usuários podem sacar até fazerem o primeiro depósito
        ]);
        });
    }
}

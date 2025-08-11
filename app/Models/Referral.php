<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'registered_at',
        'total_losses',
        'total_deposits',
        'total_commission'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'total_losses' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_commission' => 'decimal:2'
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
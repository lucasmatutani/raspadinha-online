<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referral_id',
        'loss_amount',
        'deposit_amount',
        'commission_amount',
        'status',
        'game_details',
        'deposit_details'
    ];

    protected $casts = [
        'loss_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'game_details' => 'array',
        'deposit_details' => 'array'
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
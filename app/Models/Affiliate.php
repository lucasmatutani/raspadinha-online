<?php

// app/Models/Affiliate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_affiliate_id',
        'affiliate_code',
        'commission_rate',
        'sub_affiliate_commission_rate',
        'status',
        'total_earnings',
        'pending_earnings',
        'total_sub_affiliate_earnings',
        'pending_sub_affiliate_earnings'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'sub_affiliate_commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'total_sub_affiliate_earnings' => 'decimal:2',
        'pending_sub_affiliate_earnings' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    // Relacionamento com afiliado pai
    public function parentAffiliate()
    {
        return $this->belongsTo(Affiliate::class, 'parent_affiliate_id');
    }

    // Relacionamento com subafiliados
    public function subAffiliates()
    {
        return $this->hasMany(Affiliate::class, 'parent_affiliate_id');
    }

    // Subafiliados ativos (que têm pelo menos 1 referral)
    public function activeSubAffiliates()
    {
        return $this->subAffiliates()->whereHas('referrals');
    }

    public function getReferralLinkAttribute()
    {
        return url('/ref/' . $this->affiliate_code);
    }

    public function getTotalReferralsAttribute()
    {
        return $this->referrals()->count();
    }

    public function getActiveReferralsAttribute()
    {
        return $this->referrals()
            ->whereHas('referredUser', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->count();
    }

    public static function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('affiliate_code', $code)->exists());

        return $code;
    }
}

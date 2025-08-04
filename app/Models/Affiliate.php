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
        'affiliate_code',
        'commission_rate',
        'status',
        'total_earnings',
        'pending_earnings'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2'
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

// app/Models/Referral.php
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
        'total_commission'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'total_losses' => 'decimal:2',
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

// app/Models/Commission.php
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
        'commission_amount',
        'status',
        'game_details'
    ];

    protected $casts = [
        'loss_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'game_details' => 'array'
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

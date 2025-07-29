<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScratchCard extends Model
{
    protected $fillable = [
        'user_id',
        'bet_amount', 
        'prize_amount',
        'symbols',
        'is_winner',
        'played_at'
    ];

    protected $casts = [
        'symbols' => 'array',
        'played_at' => 'datetime',
        'bet_amount' => 'decimal:2',
        'prize_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

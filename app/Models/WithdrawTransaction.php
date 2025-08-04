<?php
// app/Models/WithdrawTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'status',
        'amount',
        'fee',
        'ispb',
        'nome_recebedor',
        'cpf_recebedor',
        'callback_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'callback_data' => 'array',
    ];
}
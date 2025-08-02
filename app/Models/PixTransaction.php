<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway_transaction_id',
        'type',
        'status',
        'amount',
        'fee',
        'net_amount',
        'qr_code',
        'qr_code_image',
        'pix_key',
        'pix_key_type',
        'payer_info',
        'gateway_response',
        'callback_data',
        'callback_attempts',
        'last_callback_at',
        'expires_at',
        'completed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payer_info' => 'array',
        'gateway_response' => 'array',
        'callback_data' => 'array',
        'callback_attempts' => 'integer',
        'last_callback_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes úteis
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Métodos úteis
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdrawal';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed'
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);
    }

    public function incrementCallbackAttempts(): void
    {
        $this->increment('callback_attempts');
        $this->update(['last_callback_at' => now()]);
    }

    // Formatação para display
    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->net_amount, 2, ',', '.');
    }

    public function getFormattedFeeAttribute(): string
    {
        return 'R$ ' . number_format($this->fee, 2, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'completed' => 'Concluído',
            'failed' => 'Falhou',
            'cancelled' => 'Cancelado',
            'expired' => 'Expirado',
            default => 'Desconhecido'
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'Depósito',
            'withdrawal' => 'Saque',
            default => 'Desconhecido'
        };
    }
}
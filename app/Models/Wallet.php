<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 
        'balance', 
        'total_deposited', 
        'total_wagered', 
        'rollover_requirement', 
        'rollover_completed', 
        'can_withdraw'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_deposited' => 'decimal:2',
        'total_wagered' => 'decimal:2',
        'rollover_requirement' => 'decimal:2',
        'rollover_completed' => 'decimal:2',
        'can_withdraw' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function addFunds($amount)
    {
        $this->increment('balance', $amount);
    }
    
    public function deductFunds($amount)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Adiciona um depósito e atualiza o requisito de rollover
     * O rollover só é aplicado a partir do primeiro depósito
     */
    public function addDeposit($amount)
    {
        // Verificar se é o primeiro depósito ou se já existe rollover ativo
        $isFirstDeposit = $this->total_deposited == 0;
        $hasActiveRollover = $this->rollover_requirement > 0;
        
        $this->increment('balance', $amount);
        $this->increment('total_deposited', $amount);
        
        // Aplicar rollover apenas para novos usuários (primeiro depósito) ou usuários que já têm rollover ativo
        if ($isFirstDeposit || $hasActiveRollover) {
            $this->increment('rollover_requirement', $amount); // 100% do depósito
            $this->update(['can_withdraw' => $this->checkCanWithdraw()]);
        }
        // Usuários existentes que nunca tiveram rollover continuam podendo sacar
    }

    /**
     * Registra uma aposta para o rollover
     */
    public function addWager($amount)
    {
        $this->increment('total_wagered', $amount);
        
        // Atualiza o rollover completado (não pode exceder o requisito)
        $newCompleted = min(
            $this->rollover_completed + $amount,
            $this->rollover_requirement
        );
        
        $this->update([
            'rollover_completed' => $newCompleted,
            'can_withdraw' => $this->checkCanWithdraw()
        ]);
    }

    /**
     * Verifica se o usuário pode sacar
     */
    public function checkCanWithdraw(): bool
    {
        return $this->rollover_completed >= $this->rollover_requirement;
    }

    /**
     * Retorna o valor restante para completar o rollover
     */
    public function getRemainingRollover(): float
    {
        return max(0, $this->rollover_requirement - $this->rollover_completed);
    }

    /**
     * Retorna o percentual de rollover completado
     */
    public function getRolloverPercentage(): float
    {
        if ($this->rollover_requirement <= 0) {
            return 100;
        }
        
        return min(100, ($this->rollover_completed / $this->rollover_requirement) * 100);
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pix_transactions', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informações básicas da transação
            $table->string('gateway_transaction_id')->unique(); // ID do The Key Club
            $table->enum('type', ['deposit', 'withdrawal'])->default('deposit');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'expired'])->default('pending');
            
            // Valores financeiros
            $table->decimal('amount', 10, 2); // Valor solicitado
            $table->decimal('fee', 10, 2)->default(0); // Taxa cobrada
            $table->decimal('net_amount', 10, 2); // Valor líquido (amount - fee)
            
            // Informações do PIX (para depósitos)
            $table->text('qr_code')->nullable(); // Código PIX
            $table->text('qr_code_image')->nullable(); // Base64 da imagem QR (se houver)
            
            // Informações do saque (para withdrawals)
            $table->string('pix_key')->nullable(); // Chave PIX para saque
            $table->string('pix_key_type')->nullable(); // Tipo da chave (cpf, email, phone, random)
            
            // Dados do pagador/recebedor
            $table->json('payer_info')->nullable(); // Informações do pagador (nome, email, documento)
            
            // Metadados do gateway
            $table->json('gateway_response')->nullable(); // Resposta completa do gateway
            $table->json('callback_data')->nullable(); // Dados recebidos no callback
            
            // Controle de tentativas
            $table->integer('callback_attempts')->default(0); // Quantas vezes o callback foi chamado
            $table->timestamp('last_callback_at')->nullable(); // Último callback recebido
            
            // Datas importantes
            $table->timestamp('expires_at')->nullable(); // Quando o PIX expira
            $table->timestamp('completed_at')->nullable(); // Quando foi completado
            $table->timestamp('cancelled_at')->nullable(); // Quando foi cancelado
            
            $table->timestamps();
            
            // Índices para performance
            $table->index('gateway_transaction_id');
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_transactions');
    }
};
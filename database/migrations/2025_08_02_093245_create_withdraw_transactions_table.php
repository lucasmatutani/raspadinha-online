<?php
// database/migrations/2024_create_withdraw_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('withdraw_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->index();
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'CANCELLED'])->default('PENDING');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 8, 2)->nullable();
            $table->string('ispb')->nullable();
            $table->string('nome_recebedor')->nullable();
            $table->string('cpf_recebedor')->nullable();
            $table->json('callback_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdraw_transactions');
    }
};
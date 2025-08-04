<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabela de afiliados
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('affiliate_code', 20)->unique();
            $table->decimal('commission_rate', 5, 2)->default(50.00); // 50%
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('pending_earnings', 10, 2)->default(0);
            $table->timestamps();
        });

        // Tabela de referências/indicações
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->decimal('total_losses', 10, 2)->default(0); // Total perdido pelo indicado
            $table->decimal('total_commission', 10, 2)->default(0); // Total de comissão gerada
            $table->timestamps();
            
            $table->unique(['affiliate_id', 'referred_user_id']);
        });

        // Tabela de comissões
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referral_id')->constrained()->onDelete('cascade');
            $table->decimal('loss_amount', 10, 2); // Valor perdido pelo indicado
            $table->decimal('commission_amount', 10, 2); // 50% da perda
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->json('game_details')->nullable(); // Detalhes do jogo que gerou a comissão
            $table->timestamps();
        });

        // Adicionar coluna de indicação na tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referred_by_code', 20)->nullable()->after('email');
            $table->index('referred_by_code');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['referred_by_code']);
            $table->dropColumn('referred_by_code');
        });
        
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('affiliates');
    }
};
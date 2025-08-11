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
        Schema::table('affiliates', function (Blueprint $table) {
            // Adicionar coluna para afiliado pai (subafiliado)
            $table->foreignId('parent_affiliate_id')->nullable()->after('user_id')->constrained('affiliates')->onDelete('set null');
            
            // Adicionar campos para controle de comissões de subafiliados
            $table->decimal('sub_affiliate_commission_rate', 5, 2)->default(10.00)->after('commission_rate'); // 10% sobre comissões dos subafiliados
            $table->decimal('total_sub_affiliate_earnings', 10, 2)->default(0)->after('pending_earnings');
            $table->decimal('pending_sub_affiliate_earnings', 10, 2)->default(0)->after('total_sub_affiliate_earnings');
            
            // Índice para melhor performance
            $table->index('parent_affiliate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropForeign(['parent_affiliate_id']);
            $table->dropIndex(['parent_affiliate_id']);
            $table->dropColumn([
                'parent_affiliate_id',
                'sub_affiliate_commission_rate',
                'total_sub_affiliate_earnings',
                'pending_sub_affiliate_earnings'
            ]);
        });
    }
};

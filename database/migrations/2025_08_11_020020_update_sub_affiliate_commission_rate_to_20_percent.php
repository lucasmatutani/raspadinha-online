<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar a taxa padrão de comissão de subafiliados para 20%
        DB::table('affiliates')
            ->update(['sub_affiliate_commission_rate' => 20.00]);
            
        // Alterar o valor padrão da coluna para novos registros
        Schema::table('affiliates', function (Blueprint $table) {
            $table->decimal('sub_affiliate_commission_rate', 5, 2)->default(20.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o valor padrão anterior (10%)
        DB::table('affiliates')
            ->update(['sub_affiliate_commission_rate' => 10.00]);
            
        Schema::table('affiliates', function (Blueprint $table) {
            $table->decimal('sub_affiliate_commission_rate', 5, 2)->default(10.00)->change();
        });
    }
};

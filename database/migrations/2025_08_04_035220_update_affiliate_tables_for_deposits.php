<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Atualizar tabela referrals
        Schema::table('referrals', function (Blueprint $table) {
            // Adicionar coluna para total de depósitos
            if (!Schema::hasColumn('referrals', 'total_deposits')) {
                $table->decimal('total_deposits', 10, 2)->default(0)->after('total_losses');
            }
        });

        // Atualizar tabela commissions
        Schema::table('commissions', function (Blueprint $table) {
            // Adicionar colunas para depósitos
            if (!Schema::hasColumn('commissions', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('loss_amount');
            }
            if (!Schema::hasColumn('commissions', 'deposit_details')) {
                $table->json('deposit_details')->nullable()->after('game_details');
            }
        });
    }

    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasColumn('referrals', 'total_deposits')) {
                $table->dropColumn('total_deposits');
            }
        });

        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'deposit_amount')) {
                $table->dropColumn('deposit_amount');
            }
            if (Schema::hasColumn('commissions', 'deposit_details')) {
                $table->dropColumn('deposit_details');
            }
        });
    }
};
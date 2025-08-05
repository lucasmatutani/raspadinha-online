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
        Schema::table('wallets', function (Blueprint $table) {
            // Campos para controle de rollover
            $table->decimal('total_deposited', 10, 2)->default(0)->after('balance');
            $table->decimal('total_wagered', 10, 2)->default(0)->after('total_deposited');
            $table->decimal('rollover_requirement', 10, 2)->default(0)->after('total_wagered');
            $table->decimal('rollover_completed', 10, 2)->default(0)->after('rollover_requirement');
            $table->boolean('can_withdraw')->default(false)->after('rollover_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'total_deposited',
                'total_wagered', 
                'rollover_requirement',
                'rollover_completed',
                'can_withdraw'
            ]);
        });
    }
};
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
        // Para usuários existentes (que já possuem carteira), 
        // definir can_withdraw como true para não serem afetados pelo rollover
        DB::table('wallets')
            ->where('created_at', '<', now())
            ->update([
                'can_withdraw' => true,
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o padrão da migração anterior
        DB::table('wallets')
            ->update([
                'can_withdraw' => true,
                'updated_at' => now()
            ]);
    }
};
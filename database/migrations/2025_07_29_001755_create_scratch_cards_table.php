<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('scratch_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('bet_amount', 8, 2)->default(5.00);
            $table->decimal('prize_amount', 8, 2)->default(0);
            $table->json('symbols'); // Grid 3x3
            $table->boolean('is_winner')->default(false);
            $table->timestamp('played_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scratch_cards');
    }
};

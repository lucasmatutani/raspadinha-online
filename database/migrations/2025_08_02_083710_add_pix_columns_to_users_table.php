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
        Schema::table('users', function (Blueprint $table) {
            $table->string('document')->nullable()->after('password');
            $table->string('pix_key')->nullable()->after('document');
            $table->enum('key_type', ['cpf', 'cnpj', 'email', 'phone', 'random'])->nullable()->after('pix_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['document', 'pix_key', 'key_type']);
        });
    }
};
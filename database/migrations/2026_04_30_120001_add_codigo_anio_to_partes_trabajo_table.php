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
        Schema::table('partes_trabajo', function (Blueprint $table) {
            $table->string('codigo', 10)->nullable()->after('numero'); // TEC, 2G
            $table->integer('anio')->nullable()->after('codigo'); // 2025, 2026, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partes_trabajo', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'anio']);
        });
    }
};

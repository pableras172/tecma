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
        Schema::table('lineas_parte_trabajo', function (Blueprint $table) {
            $table->time('hora_entrada_pers')->nullable()->after('hora_salida');
            $table->time('hora_salida_pers')->nullable()->after('hora_entrada_pers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lineas_parte_trabajo', function (Blueprint $table) {
            $table->dropColumn(['hora_entrada_pers', 'hora_salida_pers']);
        });
    }
};

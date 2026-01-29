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
        Schema::create('linea_parte_trabajo_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linea_parte_trabajo_id')->constrained('lineas_parte_trabajo')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['linea_parte_trabajo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_parte_trabajo_user');
    }
};

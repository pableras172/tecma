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
        Schema::create('secuencias_parte', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10); // TEC, 2G, etc.
            $table->integer('anio'); // 2025, 2026, 2027, etc.
            $table->integer('secuencia')->default(0); // 1, 2, 3, ...
            $table->timestamps();
            
            // Índice único para código + año
            $table->unique(['codigo', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secuencias_parte');
    }
};

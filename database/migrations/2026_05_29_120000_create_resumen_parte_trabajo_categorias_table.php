<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumen_parte_trabajo_categorias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parte_trabajo_id')
                ->constrained('parte_trabajo')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('categoria_profesional_id')->nullable();
            $table->foreign('categoria_profesional_id', 'fk_resumen_cat_prof')
                ->references('id')
                ->on('categoria_profesional')
                ->nullOnDelete();

            $table->string('categoria_nombre');

            $table->decimal('horas_viaje', 8, 2)->default(0);
            $table->decimal('horas_trabajo', 8, 2)->default(0);
            $table->decimal('ht1', 8, 2)->default(0);
            $table->decimal('ht2', 8, 2)->default(0);
            $table->decimal('hve', 8, 2)->default(0);

            $table->timestamps();

            $table->index(['parte_trabajo_id', 'categoria_profesional_id'], 'idx_resumen_parte_categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumen_parte_trabajo_categorias');
    }
};

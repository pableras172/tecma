<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lineas_parte_trabajo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parte_trabajo_id')
                ->constrained('partes_trabajo')
                ->cascadeOnDelete();

            // Fecha de la línea
            $table->date('fecha');

            // Horarios
            $table->time('hora_ida')->nullable();
            $table->time('hora_llegada')->nullable();

            $table->time('hora_inicio_trabajo')->nullable();
            $table->time('hora_fin_trabajo')->nullable();

            $table->time('hora_inicio_trabajo_2')->nullable();
            $table->time('hora_fin_trabajo_2')->nullable();

            $table->time('hora_vuelta')->nullable();
            $table->time('hora_vuelta_llegada')->nullable();

            $table->time('hora_entrada')->nullable(); // H.E
            $table->time('hora_salida')->nullable();  // H.S

            // Horas calculadas / introducidas
            $table->decimal('ht1', 5, 2)->default(0);
            $table->decimal('ht2', 5, 2)->default(0);
            $table->decimal('hve', 5, 2)->default(0);

            // Desplazamientos
            $table->integer('kms')->default(0);

            // Dietas
            $table->boolean('media_dieta')->default(false); // 1/2 DC
            $table->boolean('dieta_completa')->default(false); // DC
            $table->boolean('hotel')->default(false);

            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices útiles
            $table->index(['parte_trabajo_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lineas_parte_trabajo');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parte_trabajo', function (Blueprint $table) {
            $table->id();

            // Identificación del parte
            $table->string('numero')->unique(); // Ej: 2G-2025-0066
            $table->date('fecha_parte');

            // Relaciones principales
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->foreignId('planta_id')
                ->constrained('plantas')
                ->cascadeOnDelete();

            // Tipo de trabajo (avería, mantenimiento, etc.)
            $table->foreignId('tipo_trabajo_id')
                ->nullable()
                ->constrained('tipos_trabajo')
                ->nullOnDelete();

            // Datos técnicos del motor
            $table->integer('horas_motor')->nullable();
            $table->integer('arranques')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_motor')->nullable();

            // Responsable y estado
            $table->foreignId('user_responsable_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('estado', [
                'borrador',
                'cerrado',
                'facturado',
            ])->default('borrador');

            // Textos largos
            $table->text('comentarios')->nullable();
            $table->text('trabajo_realizado')->nullable();

            // Totales (opcional pero recomendable)
            $table->decimal('total_horas_viaje', 6, 2)->default(0);
            $table->decimal('total_horas_trabajo', 6, 2)->default(0);
            $table->decimal('total_ht1', 6, 2)->default(0);
            $table->decimal('total_ht2', 6, 2)->default(0);
            $table->decimal('total_hve', 6, 2)->default(0);
            $table->integer('total_km')->default(0);
            $table->integer('total_media_dieta')->default(0);
            $table->integer('total_dieta')->default(0);
            $table->integer('total_hotel')->default(0);
            $table->string('firma_tecnico')->nullable();
            $table->string('firma_supervisor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes_trabajo');
    }
};

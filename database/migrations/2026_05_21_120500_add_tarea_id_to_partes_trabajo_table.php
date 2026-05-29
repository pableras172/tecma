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
        Schema::table('parte_trabajo', function (Blueprint $table) {
            $table->foreignId('tarea_id')
                ->nullable()
                ->after('anio')
                ->constrained('tareas')
                ->nullOnDelete()
                ->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parte_trabajo', function (Blueprint $table) {
            $table->dropForeign(['tarea_id']);
            $table->dropUnique(['tarea_id']);
            $table->dropColumn('tarea_id');
        });
    }
};

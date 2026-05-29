<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->date('fecha_fin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tareas')
            ->whereNull('fecha_fin')
            ->update(['fecha_fin' => DB::raw('fecha_inicio')]);

        Schema::table('tareas', function (Blueprint $table) {
            $table->date('fecha_fin')->nullable(false)->change();
        });
    }
};

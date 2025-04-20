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
            $table->string('dni')->nullable()->after('email');
            $table->string('telefono')->nullable()->after('dni');
            $table->string('direccion')->nullable()->after('telefono');
            $table->date('fecha_nacimiento')->nullable()->after('direccion');
            $table->date('fecha_ingreso')->nullable()->after('fecha_nacimiento');
            $table->foreignId('categoria_profesional_id')->nullable()->constrained('categoria_profesional')->nullOnDelete()->after('fecha_ingreso');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->after('categoria_profesional_id');
            $table->string('foto')->nullable()->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

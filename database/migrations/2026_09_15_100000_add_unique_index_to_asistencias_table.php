<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. En caso de que existan registros duplicados históricos, conservar el ID más reciente
        DB::statement("
            DELETE a1 FROM asistencias a1
            INNER JOIN asistencias a2 
            WHERE a1.id < a2.id 
              AND a1.nombre = a2.nombre 
              AND a1.fecha = a2.fecha
        ");

        // 2. Añadir índice único compuesto para posibilitar upsert de alta velocidad y búsquedas indexadas
        Schema::table('asistencias', function (Blueprint $table) {
            $table->unique(['nombre', 'fecha'], 'asistencias_nombre_fecha_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropUnique('asistencias_nombre_fecha_unique');
        });
    }
};

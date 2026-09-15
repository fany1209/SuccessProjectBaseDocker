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
        Schema::table('asistencias', function (Blueprint $table) {
            if (!Schema::hasColumn('asistencias', 'tipo')) {
                $table->string('tipo', 50)->nullable()->default('Normal')->after('salida_final');
            }
            if (!Schema::hasColumn('asistencias', 'comentario')) {
                $table->text('comentario')->nullable()->after('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            if (Schema::hasColumn('asistencias', 'comentario')) {
                $table->dropColumn('comentario');
            }
            if (Schema::hasColumn('asistencias', 'tipo')) {
                $table->dropColumn('tipo');
            }
        });
    }
};

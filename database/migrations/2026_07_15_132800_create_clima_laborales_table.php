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
        Schema::create('clima_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('q1_ambiente');
            $table->tinyInteger('q2_respeto');
            $table->tinyInteger('q3_comunicacion_oportuna');
            $table->tinyInteger('q4_comunicacion_escucha');
            $table->tinyInteger('q5_liderazgo');
            $table->tinyInteger('q6_reconocimiento');
            $table->tinyInteger('q7_desarrollo');
            $table->tinyInteger('q8_motivacion');
            $table->tinyInteger('q9_satisfaccion');
            $table->tinyInteger('q10_bienestar_carga');
            $table->tinyInteger('q11_bienestar_preocupacion');
            $table->text('q12_sugerencias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clima_laborales');
    }
};

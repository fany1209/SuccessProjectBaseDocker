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
        Schema::create('observaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inspection_id')->index('idx_observaciones_inspection');
            $table->string('name')->nullable();
            $table->enum('rev', ['cumple', 'no_cumple'])->nullable()->index('idx_observaciones_rev');
            $table->date('fecha')->nullable()->index('idx_observaciones_fecha');
            $table->string('ubicacion')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->string('ev_corr_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observaciones');
    }
};

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
        Schema::create('vitayela_productions', function (Blueprint $table) {
            $table->id('vitayela_production_id');
            $table->date('fecha_preparacion')->nullable();
            $table->decimal('kg_preparados', 10, 2)->nullable();
            $table->date('fecha_ensacado')->nullable();
            $table->decimal('kg_ensacados', 10, 2)->nullable();
            $table->integer('num_sacos')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitayela_productions');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('año');
            $table->integer('semana');
            $table->string('empresa')->nullable();
            $table->string('cantidad', 50)->nullable();
            $table->string('producto')->nullable();
            $table->string('po', 100)->nullable();
            $table->date('fecha_de_carga')->nullable();
            $table->time('hora')->nullable();
            $table->date('fecha_de_envio')->nullable();
            $table->date('fecha_requerida_por_el_cliente')->nullable();
            $table->string('transporte', 100)->nullable();
            $table->string('estatus_almacen', 100)->nullable();
            $table->string('estatus_calidad', 100)->nullable()->default('Pendiente');
            $table->string('estatus_administrativo', 100)->nullable()->default('Documentacion Pendiente');
            $table->string('documentacion_requerida')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('pdf_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

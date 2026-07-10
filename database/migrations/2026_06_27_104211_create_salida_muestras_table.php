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
        Schema::create('salida_muestras', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('folio_muestra', 50);
            $table->integer('product_id');
            $table->date('fecha_salida')->nullable();
            $table->string('nombre_comercial', 150)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('lote', 100)->nullable();
            $table->string('um', 50)->nullable();
            $table->string('cantidad', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('motivo_salida', 50)->nullable();
            $table->string('motivo_otro')->nullable();
            $table->boolean('entrega_paqueteria')->nullable()->default(false);
            $table->boolean('entrega_recoleccion_planta')->nullable()->default(false);
            $table->boolean('entrega_personal_empresa')->nullable()->default(false);
            $table->boolean('entrega_otro')->nullable()->default(false);
            $table->string('entrega_otro_txt')->nullable();
            $table->string('paq_empresa', 150)->nullable();
            $table->string('paq_guia', 100)->nullable();
            $table->string('dest_nombre')->nullable();
            $table->string('dest_direccion')->nullable();
            $table->string('dest_recibe')->nullable();
            $table->string('dest_correo', 150)->nullable();
            $table->string('dest_telefono', 50)->nullable();
            $table->boolean('docs_cc')->nullable()->default(false);
            $table->boolean('docs_ft')->nullable()->default(false);
            $table->boolean('docs_hs')->nullable()->default(false);
            $table->boolean('docs_otro')->nullable()->default(false);
            $table->string('docs_otro_txt')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_muestras');
    }
};

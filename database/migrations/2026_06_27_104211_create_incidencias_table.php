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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio', 32)->unique('uq_incidencias_folio');
            $table->unsignedBigInteger('supplier_id')->index('idx_supplier_id');
            $table->unsignedBigInteger('product_id')->index('idx_product_id');
            $table->string('supplier_name', 150)->nullable();
            $table->string('product_name')->nullable();
            $table->date('fecha_recepcion')->nullable();
            $table->date('fecha_reporte')->nullable();
            $table->enum('categoria', ['MP', 'PT', 'PP']);
            $table->string('lote', 100)->nullable();
            $table->enum('lote_tipo', ['interno', 'proveedor'])->default('interno')->comment('Origen del lote');
            $table->string('remitidos', 50);
            $table->date('fecha_incidencia')->nullable();
            $table->string('incidencia', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('firma_nombre', 150)->nullable();
            $table->json('imagenes')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['fecha_incidencia', 'fecha_reporte'], 'idx_fechas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};

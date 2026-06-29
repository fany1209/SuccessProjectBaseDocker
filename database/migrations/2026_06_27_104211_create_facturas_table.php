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
        Schema::create('facturas', function (Blueprint $table) {
            $table->bigIncrements('factura_id');
            $table->string('empresa');
            $table->enum('tipo_documento', ['factura', 'nota_venta'])->nullable()->default('factura');
            $table->string('folio_factura', 100);
            $table->decimal('subtotal', 14, 5)->nullable();
            $table->decimal('descuento_total', 14, 5)->nullable();
            $table->decimal('iva', 14, 5)->nullable();
            $table->decimal('traslado_total', 14, 5)->nullable();
            $table->decimal('retencion_total', 14, 5)->nullable();
            $table->decimal('total', 14, 5)->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('banco', 100)->nullable();
            $table->string('pagador')->nullable();
            $table->string('terminacion', 4)->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_factura')->nullable();
            $table->enum('insumo', ['directo', 'indirecto'])->default('directo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

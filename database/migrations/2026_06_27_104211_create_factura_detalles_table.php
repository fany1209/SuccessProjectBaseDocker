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
        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->bigIncrements('detalle_id');
            $table->unsignedBigInteger('factura_id')->index('idx_factura_id');
            $table->string('producto');
            $table->string('clave_sat', 50)->nullable();
            $table->string('unidad', 50)->nullable();
            $table->decimal('cantidad', 14, 5);
            $table->decimal('precio_unitario', 14, 5)->nullable();
            $table->decimal('precio', 14, 5)->nullable();
            $table->decimal('subtotal', 14, 5)->nullable();
            $table->boolean('aplica_iva')->nullable()->default(true);
            $table->decimal('iva_porcentaje', 5)->nullable()->default(16);
            $table->decimal('otro_impuesto_porcentaje', 10)->nullable()->default(0);
            $table->decimal('impuesto_total', 14, 5)->nullable();
            $table->decimal('traslado', 14, 5)->nullable();
            $table->decimal('retencion', 14, 5)->nullable();
            $table->decimal('descuento', 14, 5)->nullable();
            $table->decimal('isr', 14, 5)->nullable();
            $table->decimal('ilc', 14, 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_detalles');
    }
};

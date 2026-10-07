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
        Schema::create('production_warehouse_transfers', function (Blueprint $table) {
            $table->id('transfer_id');
            $table->string('area', 50)->comment('fertil, vitayela');
            $table->unsignedBigInteger('production_id')->nullable()->comment('ID de la produccion o inventario origen');
            $table->unsignedBigInteger('product_id')->nullable()->comment('ID de producto genérico si aplica');
            $table->string('product_name', 150);
            $table->decimal('quantity', 10, 3)->comment('Cantidad (ej. número de sacos)');
            $table->decimal('weight_per_unit', 10, 3)->default(1)->comment('Peso por unidad (ej. kg por saco)');
            $table->decimal('total_weight', 10, 3)->comment('Peso total enviado');
            $table->string('batch', 100)->nullable()->comment('Lote de producción');
            $table->string('status', 50)->default('Pendiente')->comment('Pendiente, Ingresada');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_warehouse_transfers');
    }
};

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
        Schema::create('supplier_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('insumo');
            $table->string('clave_sat', 20)->nullable();
            $table->string('proveedor');
            $table->decimal('precio', 12);
            $table->boolean('tiene_iva')->nullable()->default(true);
            $table->date('fecha_cotizacion');
            $table->string('moneda', 3)->default('MXN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_prices');
    }
};

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
        Schema::create('laboratory_samples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio', 4)->unique('folio');
            $table->string('tipo_muestra', 150);
            $table->string('proveedor')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('producto')->nullable();
            $table->decimal('stock_inicial', 10)->nullable()->default(0);
            $table->string('presentacion', 100)->nullable();
            $table->string('ubicacion_stock')->nullable();
            $table->date('fecha_entrada')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->decimal('cantidad_salida', 10)->nullable()->default(0);
            $table->decimal('stock_final', 10)->nullable()->storedAs('`stock_inicial` - `cantidad_salida`');
            $table->string('motivo_salida')->nullable();
            $table->string('solicitante')->nullable();
            $table->string('recolector')->nullable();
            $table->string('cliente')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_samples');
    }
};

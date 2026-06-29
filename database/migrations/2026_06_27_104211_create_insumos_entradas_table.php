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
        Schema::create('insumos_entradas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_llegada');
            $table->string('proveedor');
            $table->string('categoria', 50)->default('warehouse');
            $table->text('descripcion')->nullable();
            $table->decimal('cantidad', 12, 3)->default(0);
            $table->string('unidad', 20);
            $table->string('insumo');
            $table->decimal('costo', 12, 4)->default(0);
            $table->char('moneda', 3)->default('MXN');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_entradas');
    }
};

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
        Schema::create('cxp_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id')->unique();
            $table->date('fecha_pago')->nullable();
            $table->integer('semana')->nullable();
            $table->integer('anio')->nullable();
            $table->enum('estatus', ['PENDIENTE', 'PARCIAL', 'PAGADO', 'CANCELADO'])->default('PENDIENTE');
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();

            $table->foreign('factura_id')->references('factura_id')->on('facturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cxp_details');
    }
};

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
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('empresa', 150);
            $table->decimal('cantidad', 12, 5);
            $table->text('motivo');
            $table->string('banco', 100)->nullable();
            $table->string('factura', 50)->nullable();
            $table->date('fecha_factura')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->enum('estatus', ['PENDIENTE', 'PAGADO', 'CANCELADO'])->nullable()->default('PENDIENTE');
            $table->text('comentarios')->nullable();
            $table->string('terminacion', 4)->nullable();
            $table->string('efectivo', 2)->nullable();
            $table->integer('semana');
            $table->integer('anio');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_payments');
    }
};

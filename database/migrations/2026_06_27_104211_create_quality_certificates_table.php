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
        Schema::create('quality_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('producto');
            $table->string('cliente')->index('idx_certificados_envio_cliente');
            $table->date('fecha')->nullable()->index('idx_certificados_envio_fecha');
            $table->string('lote', 100)->nullable()->index('idx_certificados_envio_lote');
            $table->string('cantidad', 100)->nullable();
            $table->string('folio', 50)->unique('ux_certificados_envio_folio');
            $table->unsignedInteger('no_tarimas')->nullable();
            $table->date('fecha_salida_cedis')->nullable();
            $table->string('certificado_tarima')->nullable();
            $table->enum('muestra_o_pf', ['Muestra', 'PT'])->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_certificates');
    }
};

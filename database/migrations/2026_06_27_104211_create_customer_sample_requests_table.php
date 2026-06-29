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
        Schema::create('customer_sample_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio', 50)->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('um', 50)->nullable();
            $table->string('cantidad', 100)->nullable();
            $table->boolean('pres_ziploc')->nullable()->default(false);
            $table->boolean('pres_whirlpak')->nullable()->default(false);
            $table->boolean('pres_metalizada')->nullable()->default(false);
            $table->boolean('pres_frasco')->nullable()->default(false);
            $table->boolean('pres_bidon')->nullable()->default(false);
            $table->boolean('pres_otro')->nullable()->default(false);
            $table->string('pres_otro_txt')->nullable();
            $table->string('lote_almacen', 100)->nullable();
            $table->string('lote_venta', 100)->nullable();
            $table->date('fecha_recoleccion')->nullable();
            $table->boolean('docs_cc')->nullable()->default(false);
            $table->boolean('docs_ft')->nullable()->default(false);
            $table->boolean('docs_hs')->nullable()->default(false);
            $table->boolean('docs_otro')->nullable()->default(false);
            $table->string('docs_otro_txt')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_direccion', 500)->nullable();
            $table->string('cliente_correo')->nullable();
            $table->string('cliente_telefono', 50)->nullable();
            $table->enum('cliente_estatus', ['nuevo', 'frecuente'])->nullable();
            $table->string('personal_seguimiento')->nullable();
            $table->boolean('entrega_paqueteria')->nullable()->default(false);
            $table->boolean('entrega_personal_empresa')->nullable()->default(false);
            $table->boolean('entrega_recoleccion_planta')->nullable()->default(false);
            $table->boolean('entrega_otro')->nullable()->default(false);
            $table->string('entrega_otro_txt')->nullable();
            $table->string('paq_nombre')->nullable();
            $table->string('paq_guia')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('solicitante_nombre')->nullable();
            $table->boolean('status')->default(false)->comment('0: Pendiente, 1: En Proceso, 2: Completado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sample_requests');
    }
};

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
        Schema::create('reception_of_samples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio_muestra', 100)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('batch', 100)->nullable();
            $table->date('fecha_entrada')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('origen_muestra', ['proveedor', 'produccion', 'almacen', 'otro'])->nullable();
            $table->string('origen_otro')->nullable();
            $table->string('objetivo_muestra')->nullable();
            $table->string('objetivo_otro')->nullable();
            $table->decimal('cantidad', 10)->nullable();
            $table->enum('um', ['g', 'kg', 'l', 'ml', 'otro'])->nullable();
            $table->string('um_otro', 50)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->boolean('docs_ccf')->nullable()->default(false);
            $table->boolean('docs_ft')->nullable()->default(false);
            $table->boolean('docs_hs')->nullable()->default(false);
            $table->boolean('docs_otro')->nullable()->default(false);
            $table->string('docs_otro_txt')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('observaciones_laboratorio')->nullable();
            $table->string('firma_entrega_nombre')->nullable();
            $table->string('firma_recepcion_nombre')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('estatus')->default(false)->comment('0 = Pendiente, 1 = Terminado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_of_samples');
    }
};

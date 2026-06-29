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
        Schema::create('laboratory_monitoring', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asesor', 120)->nullable();
            $table->unsignedBigInteger('productor_id')->index('idx_productor_id');
            $table->string('cultivo', 120)->nullable();
            $table->string('folio', 60)->nullable()->index('idx_folio');
            $table->string('producto_aplicar')->nullable();
            $table->string('peso_text', 20)->nullable();
            $table->text('objetivo')->nullable();
            $table->text('condiciones')->nullable();
            $table->string('ubicacion', 120)->nullable();
            $table->string('ubicacion_nombre')->nullable();
            $table->text('division_bloques')->nullable();
            $table->text('tratamiento')->nullable();
            $table->json('dosis')->nullable();
            $table->date('fecha_aplicacion')->nullable()->index('idx_fecha_aplicacion');
            $table->string('doc_muestreo')->nullable();
            $table->json('fechas_muestreo')->nullable();
            $table->text('variables_agro')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_monitoring');
    }
};

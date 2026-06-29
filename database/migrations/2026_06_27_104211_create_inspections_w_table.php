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
        Schema::create('inspections_w', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_inspeccion')->nullable()->index('idx_inspections_fecha');
            $table->string('inspector')->nullable();
            $table->time('hora_turno')->nullable();
            $table->string('turno', 20)->nullable()->index('idx_inspections_turno');
            $table->longText('area')->nullable();
            $table->string('area_otro')->nullable();
            $table->string('responsable')->nullable();
            $table->text('comentarios')->nullable();
            $table->text('comentarios_q')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('idx_inspections_user');
            $table->timestamps();
            $table->boolean('status')->default(false)->comment('0=incompleta,1=revisada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections_w');
    }
};

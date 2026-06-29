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
        Schema::create('minutas', function (Blueprint $table) {
            $table->integer('id_minuta', true);
            $table->unsignedBigInteger('user_id')->nullable()->index('fk_minutas_user');
            $table->dateTime('fecha_hora');
            $table->string('lugar')->nullable();
            $table->string('tema_general')->nullable();
            $table->string('ponente', 100)->nullable();
            $table->text('asistente_nombre')->nullable();
            $table->string('asistente_departamento', 100)->nullable();
            $table->text('tema_tratado')->nullable();
            $table->text('acuerdo')->nullable();
            $table->text('responsable')->nullable();
            $table->text('fecha_cierre')->nullable();
            $table->text('estatus')->nullable();
            $table->text('fecha_compromiso')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutas');
    }
};

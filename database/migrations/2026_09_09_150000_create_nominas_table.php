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
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('curp', 18)->nullable()->index();
            $table->string('rfc', 13)->nullable()->index();
            $table->string('nss', 20)->nullable();
            $table->string('puesto');
            $table->date('fecha_ingreso');
            $table->date('fecha_baja')->nullable();
            $table->integer('edad')->nullable();
            $table->string('antiguedad')->nullable();
            $table->string('sexo', 20)->nullable();
            $table->string('estado_civil', 50)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('nombre_beneficiario')->nullable();
            $table->string('parentesco', 100)->nullable();
            $table->text('domicilio')->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('estatus', 20)->default('Activo')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};

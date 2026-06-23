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
        //POR TEMAS DE PRACTICA, AGREGAR LA CONSTRAINT DE LLAVE FORANEA CON UNA CONSULTA SQL EN LUGAR DE CON LAS MIGRACIONES
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('name', 200);
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('district', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('customer_code', 15)->unique();
            $table->unsignedBigInteger('sector_id')->default(1);
            $table->foreign('sector_id')->references('sector_id')->on('sectors');//COnsiderar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

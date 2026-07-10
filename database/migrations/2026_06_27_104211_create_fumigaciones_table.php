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
        Schema::create('fumigaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('proveedor', 150);
            $table->dateTime('fecha_programada');
            $table->string('metodo_aplicacion', 100)->nullable();
            $table->enum('estado', ['Pendiente', 'Realizado', 'Cancelado'])->nullable()->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fumigaciones');
    }
};

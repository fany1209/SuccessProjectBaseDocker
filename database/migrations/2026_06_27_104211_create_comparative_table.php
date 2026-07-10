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
        Schema::create('comparative', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('folio', 50)->nullable();
            $table->string('insumo');
            $table->integer('cantidad');
            $table->string('proveedor')->nullable();
            $table->decimal('precio_unt', 10)->nullable();
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('entrega_estimada')->nullable();
            $table->text('link')->nullable();
            $table->decimal('precio_total', 10)->nullable();
            $table->text('comentarios')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparative');
    }
};

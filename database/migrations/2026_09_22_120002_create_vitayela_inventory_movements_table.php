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
        Schema::create('vitayela_inventory_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->unsignedBigInteger('vitayela_inventory_id');
            $table->enum('tipo', ['Entrada', 'Salida', 'Ajuste']);
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();

            $table->foreign('vitayela_inventory_id', 'fk_vit_inv_mov_id')
                  ->references('vitayela_inventory_id')->on('vitayela_inventories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitayela_inventory_movements');
    }
};

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
        Schema::create('proteamin_inventory_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->unsignedBigInteger('proteamin_inventory_id');
            $table->enum('tipo', ['Entrada', 'Salida', 'Ajuste']);
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();

            $table->foreign('proteamin_inventory_id', 'fk_prot_inv_mov_id')
                  ->references('proteamin_inventory_id')->on('proteamin_inventories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proteamin_inventory_movements');
    }
};

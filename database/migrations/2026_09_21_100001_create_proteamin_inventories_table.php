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
        Schema::create('proteamin_inventories', function (Blueprint $table) {
            $table->id('proteamin_inventory_id');
            $table->string('producto_descripcion')->nullable();
            $table->decimal('cantidad', 10, 2)->default(0);
            $table->string('unidad')->nullable();
            $table->decimal('stock_min', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proteamin_inventories');
    }
};

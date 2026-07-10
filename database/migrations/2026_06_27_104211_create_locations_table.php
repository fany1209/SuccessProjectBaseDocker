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
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('location_id');
            $table->string('name', 10);
            $table->boolean('allergen')->default(false);
            $table->unsignedBigInteger('warehouse_id')->index('locations_warehouse_id_foreign');
            $table->timestamps();

            $table->unique(['name', 'warehouse_id'], 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

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
        Schema::create('pallet_yeast_production', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pallet_id');
            $table->unsignedBigInteger('yeast_production_id');
            $table->integer('sacks_contributed');
            $table->timestamps();

            $table->foreign('pallet_id')->references('pallet_id')->on('pallets')->onDelete('cascade');
            $table->foreign('yeast_production_id')->references('yeast_production_id')->on('yeast_productions')->onDelete('cascade');
        });
    }

    /**-
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallet_yeast_production');
    }
};

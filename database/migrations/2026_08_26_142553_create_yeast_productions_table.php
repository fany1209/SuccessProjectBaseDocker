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
        Schema::create('yeast_productions', function (Blueprint $table) {
            $table->id('yeast_production_id');
            $table->unsignedBigInteger('output_id')->nullable();
            $table->date('date')->nullable();
            $table->string('bag_number')->nullable();
            $table->decimal('internal_weight', 10, 2)->nullable();
            $table->decimal('external_weight', 10, 2)->nullable();
            $table->string('color')->nullable();
            $table->decimal('finished_product_kg', 10, 2)->nullable();
            $table->integer('bags_quantity')->nullable();
            $table->timestamps();
            
            $table->foreign('output_id')->references('output_id')->on('outputs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeast_productions');
    }
};

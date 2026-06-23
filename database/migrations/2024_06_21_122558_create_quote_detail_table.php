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
        Schema::create('quote_detail', function (Blueprint $table) {
            $table->id('quote_detail_id');
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('product_id');
            $table->string('quote_product_name',150)->nullable();
            $table->double('quantity', 11, 3);
            $table->double('cost', 10, 2);
            $table->string('presentation', 70);
            $table->foreign('quote_id')->references('quote_id')->on('quotes');
            $table->foreign('product_id')->references('product_id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_detail');
    }
};

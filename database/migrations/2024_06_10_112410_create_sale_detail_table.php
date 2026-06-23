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
        Schema::create('sale_detail', function (Blueprint $table) {
            $table->id('sale_detail_id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->string('public_product_name',150)->nullable(); //new
            $table->double('quantity', 11, 3);
            $table->double('cost', 10, 2)->nullable(); //Nullable new
            $table->boolean('invoice_val')->nullable()->default(false);
            $table->string('warehouse_batch', 20)->nullable();
            $table->string('public_batch', 20)->nullable();
            $table->foreign('sale_id')->references('sale_id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_detail');
    }
};

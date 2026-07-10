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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('name', 200);
            $table->string('description', 500)->nullable();
            $table->string('presentation', 100)->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('batch_code', 20)->nullable();
            $table->string('sat_code', 20);
            $table->string('sku', 20)->unique();
            $table->double('stock_min')->nullable();
            $table->double('stock_max')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('iva')->nullable()->default(false);
            $table->unsignedBigInteger('category_id')->index('products_category_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

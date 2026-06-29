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
            $table->bigIncrements('sale_detail_id');
            $table->unsignedBigInteger('sale_id')->index('sale_detail_sale_id_foreign');
            $table->unsignedBigInteger('product_id')->index('sale_detail_product_id_foreign');
            $table->string('public_product_name', 150)->nullable();
            $table->boolean('invoice_val')->nullable()->default(false);
            $table->boolean('has_tax')->default(false);
            $table->double('quantity');
            $table->double('cost')->nullable();
            $table->string('warehouse_batch', 20)->nullable();
            $table->string('public_batch', 20)->nullable();
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

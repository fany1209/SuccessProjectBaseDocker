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
        Schema::create('product_outputs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('product_outputs_product_id_foreign');
            $table->unsignedBigInteger('output_id')->index('product_outputs_output_id_foreign');
            $table->double('quantity')->nullable();
            $table->string('warehouse_batch', 20);
            $table->string('label_batch', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_outputs');
    }
};

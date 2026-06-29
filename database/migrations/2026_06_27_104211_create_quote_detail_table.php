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
            $table->bigIncrements('quote_detail_id');
            $table->unsignedBigInteger('quote_id')->index('quote_detail_quote_id_foreign');
            $table->unsignedBigInteger('product_id')->index('quote_detail_product_id_foreign');
            $table->string('quote_product_name', 150)->nullable();
            $table->double('quantity');
            $table->string('unit', 20)->nullable();
            $table->double('cost');
            $table->decimal('iva', 5)->default(0.16);
            $table->string('presentation', 70);
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

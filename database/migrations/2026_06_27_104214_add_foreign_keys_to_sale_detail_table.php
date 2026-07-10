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
        Schema::table('sale_detail', function (Blueprint $table) {
            $table->foreign(['sale_id'], 'fk2_sales')->references(['sale_id'])->on('sales')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['product_id'])->references(['product_id'])->on('products')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_detail', function (Blueprint $table) {
            $table->dropForeign('fk2_sales');
            $table->dropForeign('sale_detail_product_id_foreign');
        });
    }
};

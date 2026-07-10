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
        Schema::table('quote_detail', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['product_id'])->on('products')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['quote_id'])->references(['quote_id'])->on('quotes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_detail', function (Blueprint $table) {
            $table->dropForeign('quote_detail_product_id_foreign');
            $table->dropForeign('quote_detail_quote_id_foreign');
        });
    }
};

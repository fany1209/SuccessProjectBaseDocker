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
        Schema::table('product_inputs', function (Blueprint $table) {
            $table->foreign(['input_id'])->references(['input_id'])->on('inputs')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['product_id'])->references(['product_id'])->on('products')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_inputs', function (Blueprint $table) {
            $table->dropForeign('product_inputs_input_id_foreign');
            $table->dropForeign('product_inputs_product_id_foreign');
        });
    }
};

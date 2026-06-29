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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreign(['product_id'], 'fk_incidencias_product')->references(['product_id'])->on('products')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['supplier_id'], 'fk_incidencias_supplier')->references(['supplier_id'])->on('suppliers')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign('fk_incidencias_product');
            $table->dropForeign('fk_incidencias_supplier');
        });
    }
};

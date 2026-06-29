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
        Schema::table('requisition_products', function (Blueprint $table) {
            $table->foreign(['pr_id'])->references(['id'])->on('purchases_requisitions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisition_products', function (Blueprint $table) {
            $table->dropForeign('requisition_products_pr_id_foreign');
        });
    }
};

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
        Schema::table('supplier_certificates', function (Blueprint $table) {
            $table->foreign(['file_id'], 'fk_supplier_certificates_file')->references(['file_id'])->on('files')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['supplier_id'], 'fk_supplier_certificates_supplier')->references(['supplier_id'])->on('suppliers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_certificates', function (Blueprint $table) {
            $table->dropForeign('fk_supplier_certificates_file');
            $table->dropForeign('fk_supplier_certificates_supplier');
        });
    }
};

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
        Schema::table('inputs', function (Blueprint $table) {
            $table->foreign(['supplier_id'])->references(['supplier_id'])->on('suppliers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['transport_line_id'])->references(['transport_line_id'])->on('transport_lines')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inputs', function (Blueprint $table) {
            $table->dropForeign('inputs_supplier_id_foreign');
            $table->dropForeign('inputs_transport_line_id_foreign');
        });
    }
};

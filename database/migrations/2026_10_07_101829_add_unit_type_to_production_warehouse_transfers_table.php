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
        Schema::table('production_warehouse_transfers', function (Blueprint $table) {
            $table->string('unit_type', 50)->nullable()->after('product_name')->comment('Tipo de empaque o unidad: Sacos, Litros, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_warehouse_transfers', function (Blueprint $table) {
            $table->dropColumn('unit_type');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cli', 'packing_list')) {
            Schema::table('cli', function (Blueprint $table) {
                $table->string('packing_list', 50)->nullable()->after('bag_number');
            });
        }

        if (!Schema::hasColumn('product_inputs', 'packing_list')) {
            Schema::table('product_inputs', function (Blueprint $table) {
                $table->string('packing_list', 50)->nullable()->after('warehouse_batch');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cli', 'packing_list')) {
            Schema::table('cli', function (Blueprint $table) {
                $table->dropColumn('packing_list');
            });
        }

        if (Schema::hasColumn('product_inputs', 'packing_list')) {
            Schema::table('product_inputs', function (Blueprint $table) {
                $table->dropColumn('packing_list');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('product_inputs', 'total_weight')) {
            Schema::table('product_inputs', function (Blueprint $table) {
                $table->decimal('total_weight', 12, 3)->nullable()->after('quantity');
            });
        }

        // Backfill total_weight for existing BigBag product_inputs from cli / inventory
        try {
            $bigBagInputs = DB::table('product_inputs')
                ->join('products', 'products.product_id', '=', 'product_inputs.product_id')
                ->whereNull('product_inputs.total_weight')
                ->where(function ($query) {
                    $query->where('products.name', 'LIKE', '%BGBG%')
                        ->orWhere('products.name', 'LIKE', '%BIGBAG%')
                        ->orWhere('products.unit', 'LIKE', '%BIGBAG%')
                        ->orWhere('products.presentation', 'LIKE', '%BIGBAG%');
                })
                ->select(
                    'product_inputs.id',
                    'product_inputs.product_id',
                    'product_inputs.warehouse_batch',
                    'product_inputs.packing_list'
                )
                ->get();

            foreach ($bigBagInputs as $row) {
                $inv = DB::table('inventory')
                    ->where('product_id', $row->product_id)
                    ->where('batch', $row->warehouse_batch)
                    ->first();

                if ($inv) {
                    $cliQuery = DB::table('cli')->where('inventory_id', $inv->inventory_id);
                    if (!empty($row->packing_list)) {
                        $cliQuery->where('packing_list', $row->packing_list);
                    }

                    $weightSum = (float) $cliQuery->sum('net_weight');
                    if ($weightSum > 0) {
                        DB::table('product_inputs')
                            ->where('id', $row->id)
                            ->update(['total_weight' => $weightSum]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently continue if data backfill encounters historical anomalies
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_inputs', 'total_weight')) {
            Schema::table('product_inputs', function (Blueprint $table) {
                $table->dropColumn('total_weight');
            });
        }
    }
};

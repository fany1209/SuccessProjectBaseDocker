<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $locName = 'ZA' . $i;
            $exists = DB::table('locations')
                ->where('warehouse_id', 2)
                ->where('name', $locName)
                ->exists();

            if (!$exists) {
                DB::table('locations')->insert([
                    'name'         => $locName,
                    'allergen'     => 0,
                    'warehouse_id' => 2,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $names = [];
        for ($i = 1; $i <= 12; $i++) {
            $names[] = 'ZA' . $i;
        }

        DB::table('locations')
            ->where('warehouse_id', 2)
            ->whereIn('name', $names)
            ->delete();
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $names = ['Zone P', 'Zona P'];
        foreach ($names as $name) {
            $exists = DB::table('locations')
                ->where('warehouse_id', 2)
                ->where('name', $name)
                ->exists();

            if (!$exists) {
                DB::table('locations')->insert([
                    'name'         => $name,
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
        DB::table('locations')
            ->where('warehouse_id', 2)
            ->whereIn('name', ['Zone P', 'Zona P'])
            ->delete();
    }
};

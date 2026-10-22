<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            // Bloque 1 (anteriormente AC):
            // Fila superior (der -> izq): AC4 -> ZC1, AC5 -> ZC2, AC6 -> ZC3
            // Fila inferior (der -> izq): AC1 -> ZC4, AC2 -> ZC5, AC3 -> ZC6
            'AC4' => 'ZC1',
            'AC5' => 'ZC2',
            'AC6' => 'ZC3',
            'AC1' => 'ZC4',
            'AC2' => 'ZC5',
            'AC3' => 'ZC6',

            // Bloque 2 (anteriormente AB):
            // Fila superior (der -> izq): AB4 -> ZC7, AB5 -> ZC8, AB6 -> ZC9
            // Fila inferior (der -> izq): AB1 -> ZC10, AB2 -> ZC11, AB3 -> ZC12
            'AB4' => 'ZC7',
            'AB5' => 'ZC8',
            'AB6' => 'ZC9',
            'AB1' => 'ZC10',
            'AB2' => 'ZC11',
            'AB3' => 'ZC12',

            // Bloque 3 (anteriormente AA):
            // Fila superior (der -> izq): AA4 -> ZC13, AA5 -> ZC14, AA6 -> ZC15
            // Fila inferior (der -> izq): AA1 -> ZC16, AA2 -> ZC17, AA3 -> ZC18
            'AA4' => 'ZC13',
            'AA5' => 'ZC14',
            'AA6' => 'ZC15',
            'AA1' => 'ZC16',
            'AA2' => 'ZC17',
            'AA3' => 'ZC18',
        ];

        foreach ($mapping as $oldName => $newName) {
            DB::table('locations')
                ->where('warehouse_id', 2)
                ->where('name', $oldName)
                ->update(['name' => $newName]);
        }

        // Create ZC19 through ZC24 if they don't exist
        for ($i = 19; $i <= 24; $i++) {
            $locName = 'ZC' . $i;
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
        $reverseMapping = [
            'ZC1'  => 'AC4',
            'ZC2'  => 'AC5',
            'ZC3'  => 'AC6',
            'ZC4'  => 'AC1',
            'ZC5'  => 'AC2',
            'ZC6'  => 'AC3',
            'ZC7'  => 'AB4',
            'ZC8'  => 'AB5',
            'ZC9'  => 'AB6',
            'ZC10' => 'AB1',
            'ZC11' => 'AB2',
            'ZC12' => 'AB3',
            'ZC13' => 'AA4',
            'ZC14' => 'AA5',
            'ZC15' => 'AA6',
            'ZC16' => 'AA1',
            'ZC17' => 'AA2',
            'ZC18' => 'AA3',
        ];

        foreach ($reverseMapping as $currentName => $originalName) {
            DB::table('locations')
                ->where('warehouse_id', 2)
                ->where('name', $currentName)
                ->update(['name' => $originalName]);
        }

        // Delete ZC19..ZC24 for warehouse 2
        DB::table('locations')
            ->where('warehouse_id', 2)
            ->whereIn('name', ['ZC19', 'ZC20', 'ZC21', 'ZC22', 'ZC23', 'ZC24'])
            ->delete();
    }
};


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Equipment;
use App\Models\MaintenancePlan;
use App\Models\ChecklistTemplateItem;
use App\Models\MaintenanceRecord;

class MaintenanceSeeder extends Seeder
{
    public function run()
    {
        $area = Area::firstOrCreate(['name' => 'Producción'], ['description' => 'Área principal de producción']);
        
        $equipment = Equipment::firstOrCreate(
            ['code' => 'MNT-001'],
            [
                'area_id' => $area->id,
                'name' => 'Montacargas Toyota',
                'is_active' => true
            ]
        );

        $plan = MaintenancePlan::firstOrCreate(
            ['name' => 'Mantenimiento Preventivo Mensual', 'equipment_id' => $equipment->id],
            [
                'type' => 'frequent',
                'frequency_days' => 30
            ]
        );

        if ($plan->checklistItems()->count() == 0) {
            ChecklistTemplateItem::create(['maintenance_plan_id' => $plan->id, 'description' => 'Revisar niveles de aceite y fluidos']);
            ChecklistTemplateItem::create(['maintenance_plan_id' => $plan->id, 'description' => 'Inspección de llantas y presión']);
            ChecklistTemplateItem::create(['maintenance_plan_id' => $plan->id, 'description' => 'Revisión de frenos (balatas y discos)']);
            ChecklistTemplateItem::create(['maintenance_plan_id' => $plan->id, 'description' => 'Lubricación de cadenas y rodamientos']);
        }

        if (MaintenanceRecord::count() == 0) {
            MaintenanceRecord::create([
                'equipment_id' => $equipment->id,
                'maintenance_plan_id' => $plan->id,
                'code' => 'MANT-' . date('Y') . '-0001',
                'scheduled_date' => now(),
                'status' => 'pending'
            ]);
            MaintenanceRecord::create([
                'equipment_id' => $equipment->id,
                'maintenance_plan_id' => $plan->id,
                'code' => 'MANT-' . date('Y') . '-0002',
                'scheduled_date' => now()->addDays(2),
                'status' => 'pending'
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\UpcomingMaintenanceNotification;

class CheckMaintenanceAlerts extends Command
{
    protected $signature = 'maintenance:check-alerts';

    protected $description = 'Verifica y envía alertas a los departamentos 3 días antes del mantenimiento.';

    public function handle()
    {
        // Encontrar equipos cuyo next_maintenance_date sea en exactamente 3 días o menos, 
        // y que no tengan un registro pendiente o completado para esa misma fecha (para evitar alertas duplicadas).
        $targetDate = Carbon::now()->addDays(3)->toDateString();

        $equipments = MaintenanceEquipment::whereDate('next_maintenance_date', '<=', $targetDate)
                                          ->get();

        foreach ($equipments as $equipment) {
            // Revisar si ya hay un registro de mantenimiento en proceso para esta fecha
            $existingRecord = $equipment->maintenanceRecords()
                                        ->whereDate('scheduled_date', $equipment->next_maintenance_date)
                                        ->first();

            if (!$existingRecord) {
                // Crear el registro de mantenimiento pendiente
                MaintenanceRecord::create([
                    'equipment_id' => $equipment->id,
                    'scheduled_date' => $equipment->next_maintenance_date,
                    'status' => 'pending'
                ]);

                // Notificar al departamento (usuarios con el rol correspondiente)
                if ($equipment->role_id) {
                    $users = User::role($equipment->role->name)->get();
                    foreach ($users as $user) {
                        $user->notify(new UpcomingMaintenanceNotification($equipment));
                    }
                }
            }
        }

        $this->info('Alertas de mantenimiento verificadas y enviadas correctamente.');
    }
}

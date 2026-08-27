<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipment;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceRecord;
use Carbon\Carbon;

class GenerateMaintenanceAlerts extends Command
{
    protected $signature = 'maintenance:generate-alerts';
    protected $description = 'Generate pending maintenance records based on equipment plans';

    public function handle()
    {
        $this->info('Generating maintenance alerts...');
        $plans = MaintenancePlan::with('equipment')->whereHas('equipment', function($q) {
            $q->where('is_active', true);
        })->get();

        $count = 0;
        foreach ($plans as $plan) {
            $lastRecord = MaintenanceRecord::where('maintenance_plan_id', $plan->id)
                ->orderBy('scheduled_date', 'desc')
                ->first();
            
            $nextDate = null;
            if ($lastRecord) {
                $nextDate = Carbon::parse($lastRecord->scheduled_date)->addDays($plan->frequency_days);
            } else {
                $nextDate = Carbon::today();
            }

            if ($nextDate->lte(Carbon::today()->addDays(7))) { // Generate up to a week in advance
                $exists = MaintenanceRecord::where('maintenance_plan_id', $plan->id)
                    ->whereDate('scheduled_date', $nextDate->toDateString())
                    ->exists();

                if (!$exists) {
                    $code = '#MNT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    MaintenanceRecord::create([
                        'code' => $code,
                        'equipment_id' => $plan->equipment_id,
                        'maintenance_plan_id' => $plan->id,
                        'scheduled_date' => $nextDate,
                        'status' => 'pending'
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Generated $count new maintenance alerts.");
    }
}

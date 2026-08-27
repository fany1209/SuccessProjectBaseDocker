<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'code', 'equipment_id', 'maintenance_plan_id', 'scheduled_date', 
        'status', 'printed_at', 'completed_at', 'evidence_file'
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'printed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function maintenancePlan()
    {
        return $this->belongsTo(MaintenancePlan::class);
    }
}

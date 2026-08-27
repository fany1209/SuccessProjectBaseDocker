<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplateItem extends Model
{
    protected $fillable = ['maintenance_plan_id', 'description', 'order'];

    public function maintenancePlan()
    {
        return $this->belongsTo(MaintenancePlan::class);
    }
}

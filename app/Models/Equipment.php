<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = ['code', 'name', 'area_id', 'is_active'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function maintenancePlans()
    {
        return $this->hasMany(MaintenancePlan::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}

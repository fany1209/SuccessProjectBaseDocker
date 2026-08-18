<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEquipment extends Model
{
    use HasFactory;

    protected $table = 'maintenance_equipments';

    protected $fillable = [
        'name',
        'model',
        'comments',
        'image_path',
        'frequency_days',
        'role_id',
        'next_maintenance_date',
    ];

    protected $casts = [
        'next_maintenance_date' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'equipment_id');
    }
}

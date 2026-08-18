<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $table = 'maintenance_records';

    protected $fillable = [
        'equipment_id',
        'scheduled_date',
        'department_confirmed_at',
        'department_user_id',
        'admin_confirmed_at',
        'admin_user_id',
        'status',
        'comments',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'department_confirmed_at' => 'datetime',
        'admin_confirmed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(MaintenanceEquipment::class, 'equipment_id');
    }

    public function departmentUser()
    {
        return $this->belongsTo(User::class, 'department_user_id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}

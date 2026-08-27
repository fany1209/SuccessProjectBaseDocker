<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    protected $fillable = ['equipment_id', 'name', 'frequency_days', 'type'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(ChecklistTemplateItem::class)->orderBy('order');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyWorkPlan extends Model
{
    use HasFactory;

    protected $table = 'weekly_work_plans';

    protected $fillable = [
        'week_range',
        'review_date',
        'project_name',
        'responsible_name',
        'total_hours',
    ];

    protected $casts = [
        'review_date' => 'date',
        'total_hours' => 'integer',
    ];

    public function objectives(): HasMany
    {
        return $this->hasMany(WeeklyObjective::class, 'plan_id', 'id')->orderBy('position_order');
    }

    public function results(): HasMany
    {
        return $this->hasMany(WeeklyResult::class, 'plan_id', 'id')->orderBy('position_order');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(WeeklyFinding::class, 'plan_id', 'id')->orderBy('position_order');
    }

    public function nextActions(): HasMany
    {
        return $this->hasMany(WeeklyNextAction::class, 'plan_id', 'id')->orderBy('position_order');
    }
}

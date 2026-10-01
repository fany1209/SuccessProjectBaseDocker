<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyNextAction extends Model
{
    use HasFactory;

    protected $table = 'weekly_next_actions';

    protected $fillable = [
        'plan_id',
        'next_week_range',
        'plan_text',
        'actions_text',
        'position_order',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(WeeklyWorkPlan::class, 'plan_id', 'id');
    }
}

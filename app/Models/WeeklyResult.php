<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyResult extends Model
{
    use HasFactory;

    protected $table = 'weekly_results';

    protected $fillable = [
        'plan_id',
        'objective_number',
        'result_text',
        'is_met',
        'position_order',
    ];

    protected $casts = [
        'is_met' => 'boolean',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(WeeklyWorkPlan::class, 'plan_id', 'id');
    }
}

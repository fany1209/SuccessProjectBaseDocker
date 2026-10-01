<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyFinding extends Model
{
    use HasFactory;

    protected $table = 'weekly_findings';

    protected $fillable = [
        'plan_id',
        'finding_text',
        'cause_text',
        'proposal_text',
        'position_order',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(WeeklyWorkPlan::class, 'plan_id', 'id');
    }
}

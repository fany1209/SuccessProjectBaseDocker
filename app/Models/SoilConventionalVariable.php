<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoilConventionalVariable extends Model
{
    use HasFactory;

    protected $table = 'soil_conventional_variables';

    protected $fillable = [
        'soil_analysis_id',
        'variable_name',
        'result_text',
        'unit_text',
        'position_order',
    ];

    protected $casts = [
        'position_order' => 'integer',
    ];

    /**
     * Análisis de suelo al que pertenece esta variable.
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(SoilInternalAnalysis::class, 'soil_analysis_id', 'id');
    }
}

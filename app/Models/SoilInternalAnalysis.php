<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoilInternalAnalysis extends Model
{
    use HasFactory;

    protected $table = 'soil_internal_analyses';

    protected $fillable = [
        'report_code',
        'entry_date',
        'issue_date',
        'client_name',
        'client_city',
        'client_address',
        'client_phone',
        'crop_type',
        'system_type',
        'plant_type',
        'sample_weight',
        'is_control',
        'location',
        'sampling_type',
        'sampling_responsible',
        'purpose',
        'n_value_mgkg',
        'p_value_mgkg',
        'k_value_mgkg',
        'sand_pct',
        'silt_pct',
        'clay_pct',
        'classification',
        'triangle_src',
        'image_path',
        'image_caption',
        'image_mime',
        'image_size_kb',
    ];

    protected $casts = [
        'entry_date'   => 'date',
        'issue_date'   => 'date',
        'is_control'   => 'boolean',
        'n_value_mgkg' => 'decimal:2',
        'p_value_mgkg' => 'decimal:2',
        'k_value_mgkg' => 'decimal:2',
        'sand_pct'     => 'decimal:2',
        'silt_pct'     => 'decimal:2',
        'clay_pct'     => 'decimal:2',
    ];

    /**
     * Variables convencionales asociadas al análisis de suelo.
     */
    public function conventionalVariables(): HasMany
    {
        return $this->hasMany(SoilConventionalVariable::class, 'soil_analysis_id', 'id')
            ->orderBy('position_order');
    }
}

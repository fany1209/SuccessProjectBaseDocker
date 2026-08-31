<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    protected $primaryKey = 'pallet_id';

    protected $fillable = [
        'pallet_number',
        'color_type',
        'current_sacks',
        'status',
    ];

    public function yeastProductions()
    {
        return $this->belongsToMany(YeastProduction::class, 'pallet_yeast_production', 'pallet_id', 'yeast_production_id')
                    ->withPivot('sacks_contributed')
                    ->withTimestamps();
    }
}

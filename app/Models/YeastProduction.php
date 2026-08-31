<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeastProduction extends Model
{
    protected $primaryKey = 'yeast_production_id';

    protected $fillable = [
        'output_id',
        'date',
        'bag_number',
        'internal_weight',
        'external_weight',
        'bags_natural',
        'bags_mix',
        'bags_white',
        'finished_product_kg',
        'bags_quantity',
    ];

    public function output()
    {
        return $this->belongsTo(Output::class, 'output_id');
    }

    public function pallets()
    {
        return $this->belongsToMany(Pallet::class, 'pallet_yeast_production', 'yeast_production_id', 'pallet_id')
                    ->withPivot('sacks_contributed')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cli extends Model
{
    use HasFactory;

    protected $table = 'cli';

    protected $primaryKey = 'cli_id';

    protected $fillable = [
        'bag_number', 'protein', 'quantity', 'weight_per_unit', 'net_weight', 'sq_certificate',
        'inventory_id', 'location_id', 'concept_id'
    ];

    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory', 'inventory_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function concept()
    {
        return $this->belongsTo('App\Models\Concept', 'concept_id');
    }
}

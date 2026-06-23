<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';

    protected $fillable = ['name', 'allergen', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    }

    public function clis(){
        return $this->hasMany('App\Models\Cli','location_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'warehouse_id';

    protected $fillable = ['name'];

    public function locations()
    {
        return $this->hasMany('App\Models\Location', 'warehouse_id');
    }

    public function controls()
    {
        return $this->hasMany('App\Models\Control', 'warehouse_id');
    }
}

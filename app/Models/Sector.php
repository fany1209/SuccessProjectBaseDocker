<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $primaryKey = 'sector_id';

    protected $fillable = ['name','code'];

    public $timestamps = false;

    //Remplazar categorias por sectores
    public function customers(){
        return $this->hasMany('App\Models\Customer', 'sector_id');
    }

    public function suppliers(){
        return $this->hasMany('App\Models\Supplier', 'sector_id');
    }

    public function sales(){
        return $this->hasMany('App\Models\Sale', 'sector_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $primaryKey = 'inventory_id';

    protected $fillable = ['stock', 'batch', 'bar_code', 'product_id'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function tweaks()
    {
        return $this->hasMany('App\Models\Tweak', 'inventory_id');
    }

    public function clis(){
        return $this->hasMany('App\Models\Cli','inventory_id');
    }
}

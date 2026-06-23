<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = ['name', 'description', 'presentation', 'batch_code', 'unit', 'sat_code', 'sku', 'category_id', 'stock_min', 'stock_max'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function outputs()
    {
        return $this->belongsToMany('App\Models\Output', 'product_outputs', 'product_id', 'output_id')->withPivot('quantity', 'warehouse_batch', 'label_batch');
    }

    public function inputs()
    {
        return $this->belongsToMany('App\Models\Input', 'product_inputs', 'product_id', 'input_id')->withPivot('quantity', 'warehouse_batch');
    }

    public function images()
    {
        return $this->hasMany('App\Models\Image', 'product_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\File', 'product_id');
    }

    public function inventories()
    {
        return $this->hasMany('App\Models\Inventory', 'product_id');
    }

    public function sales()
    {
        return $this->belongsToMany('App\Models\Sale', 'sale_detail', 'product_id', 'sale_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterialRequestItem extends Model
{
    use HasFactory;

    protected $table = 'production_material_request_items';

    protected $fillable = [
        'request_id',
        'product_name',
        'quantity',
        'dispatched_quantity'
    ];

    public function request()
    {
        return $this->belongsTo(ProductionMaterialRequest::class, 'request_id');
    }
}

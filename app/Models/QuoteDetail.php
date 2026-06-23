<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteDetail extends Model
{
    protected $table = 'quote_detail'; 
    protected $primaryKey = 'quote_detail_id';

    public $timestamps = false;

    protected $fillable = [
        'quote_id',
        'product_id',
        'quote_product_name',
        'quantity',
        'cost',
        'presentation',
        'unit',
        'iva' 
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
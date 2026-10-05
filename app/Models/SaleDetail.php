<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'sale_detail_id';

    protected $table = 'sale_detail';

    protected $fillable = ['product_id', 'public_product_name', 'quantity', 'cost', 'invoice_val', 'warehouse_batch', 'public_batch', 'sale_id','has_tax',];

    public $timestamps = false;

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

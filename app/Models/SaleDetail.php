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
}

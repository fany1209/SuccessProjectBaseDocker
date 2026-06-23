<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'seller', 'first_time', 'is_customer', 'purchase_order', 'invoice',
        'sale_type', 'term', 'date', 'folio', 'customer_id', 'prospect_id', 'user_id', 'sales_status_id',
        'sector_id', 'payment_status'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function prospect()
    {
        return $this->belongsTo('App\Models\Prospect', 'prospect_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'sale_detail', 'sale_id', 'product_id')->withPivot('sale_detail_id', 'public_product_name', 'invoice_val', 'quantity', 'cost', 'warehouse_batch', 'public_batch', 'has_tax');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\SaleStatus', 'sales_status_id');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector', 'sector_id');
    }
}

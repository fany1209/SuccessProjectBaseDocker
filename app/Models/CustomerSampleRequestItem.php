<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSampleRequestItem extends Model
{
    protected $table = 'customer_sample_request_items';

    protected $fillable = [
        'customer_sample_request_id',
        'product_id',
        'sku',
        'um',
        'cantidad',
        'pres_ziploc',
        'pres_whirlpak',
        'pres_metalizada',
        'pres_frasco',
        'pres_bidon',
        'pres_otro',
        'pres_otro_txt',
        'lote_almacen',
        'lote_venta',
        'docs_cc',
        'docs_ft',
        'docs_hs',
        'docs_otro',
        'docs_otro_txt'
    ];

    protected $appends = ['producto'];

    public function request()
    {
        return $this->belongsTo(CustomerSampleRequest::class, 'customer_sample_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function getProductoAttribute()
    {
        return $this->product ? $this->product->name : '';
    }

      public function getSkuAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->product ? $this->product->sku : '';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory;

    protected $primaryKey = 'output_id';

    protected $fillable = [
        'operator',
        'license_number',
        'security_seal',
        'security_seal_number',
        'unit_plates',
        'trailer_plates',
        'comments',
        'customer_id',
        'transport_line_id',
        'vendedor',      
        'created_at',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_outputs', 'output_id', 'product_id')
            ->withPivot('id', 'quantity', 'warehouse_batch', 'label_batch');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function transportLine()
    {
        return $this->belongsTo('App\Models\TransportLine', 'transport_line_id');
    }
}

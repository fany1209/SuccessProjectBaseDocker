<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    protected $primaryKey = 'input_id';

    protected $fillable = ['operator', 'license_number', 'security_seal', 'security_seal_number', 'unit_plates', 'trailer_plates', 'comments', 'supplier_id', 'transport_line_id', 'created_at'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_inputs', 'input_id', 'product_id')->withPivot('id', 'quantity', 'warehouse_batch');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function transportLine()
    {
        return $this->belongsTo('App\Models\TransportLine', 'transport_line_id');
    }
}

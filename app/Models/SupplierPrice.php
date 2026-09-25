<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    use HasFactory;

    protected $table = 'supplier_prices';

    protected $fillable = [
        'factura_id',
        'insumo',
        'clave_sat', 
        'proveedor',
        'precio',
        'tiene_iva', 
        'fecha_cotizacion',
        'moneda'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'factura_id');
    }
}
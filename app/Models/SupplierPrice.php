<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    use HasFactory;

    protected $table = 'supplier_prices';

   protected $fillable = [
        'insumo',
        'clave_sat', 
        'proveedor',
        'precio',
        'tiene_iva', 
        'fecha_cotizacion',
        'moneda'
    ];
}
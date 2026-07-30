<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaboratorySample extends Model
{
    protected $table = 'laboratory_samples';
    protected $fillable = [
        'folio','tipo_muestra','proveedor','sku','producto',
        'stock_inicial','presentacion','ubicacion_stock',
        'fecha_entrada','fecha_salida','cantidad_salida',
        'motivo_salida','solicitante','recolector','cliente',
        'status',
    ];
}


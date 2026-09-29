<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoEntrada extends Model
{
    use HasFactory;

    protected $table = 'insumos_entradas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha_llegada',
        'fecha_salida',
        'proveedor',
        'categoria',
        'descripcion',
        'cantidad',
        'unidad',
        'insumo',
        'lote',
        'lote_salida',
        'costo',
        'moneda',
    ];

    protected $casts = [
        'fecha_llegada' => 'date:Y-m-d',
        'fecha_salida'  => 'date:Y-m-d',
        'cantidad'      => 'float',
        'costo'         => 'float',
    ];
}

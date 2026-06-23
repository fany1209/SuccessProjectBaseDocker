<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comparative extends Model
{
    protected $table = 'comparative';

    protected $fillable = [
        'id',
        'user_id',
        'folio',
        'insumo',
        'cantidad',
        'proveedor',
        'precio_unt',
        'imagen',
        'descripcion',
        'entrega_estimada',
        'link',
        'precio_total',
        'comentarios'
    ];
}
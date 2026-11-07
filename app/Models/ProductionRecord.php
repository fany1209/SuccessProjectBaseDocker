<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRecord extends Model
{
    use HasFactory;

    protected $table = 'production_records';
    protected $primaryKey = 'production_record_id';

    protected $fillable = [
        'area',
        'fecha_preparacion',
        'kg_preparados',
        'fecha_ensacado',
        'kg_ensacados',
        'num_sacos',
        'descripcion',
    ];

    protected $casts = [
        'fecha_preparacion' => 'date:Y-m-d',
        'fecha_ensacado'    => 'date:Y-m-d',
        'kg_preparados'     => 'float',
        'kg_ensacados'      => 'float',
        'num_sacos'         => 'integer',
    ];
}


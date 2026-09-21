<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCertificate extends Model
{
    use HasFactory;

    protected $table = 'quality_certificates';

    protected $fillable = [
        'producto',
        'cliente',
        'fecha',
        'lote',
        'cantidad',
        'folio',
        'no_tarimas',
        'fecha_salida_cedis',
        'certificado_tarima',
        'muestra_o_pf',
    ];

    protected $casts = [
        'fecha'              => 'date:Y-m-d',
        'fecha_salida_cedis' => 'date:Y-m-d',
        'no_tarimas'         => 'integer',
    ];
}

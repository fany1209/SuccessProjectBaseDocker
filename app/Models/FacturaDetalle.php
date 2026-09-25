<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaDetalle extends Model
{
    use HasFactory;

    protected $table = 'factura_detalles';
    protected $primaryKey = 'detalle_id';

    protected $fillable = [
        'factura_id',
        'producto',
        'clave_sat',
        'unidad',
        'cantidad',
        'precio_unitario',
        'precio',
        'subtotal',
        'aplica_iva',
        'iva_porcentaje',
        'otro_impuesto_porcentaje',
        'impuesto_total',
        'traslado',
        'retencion',
        'descuento',
        'isr',
        'ilc',
    ];

    protected $casts = [
        'cantidad'                 => 'float',
        'precio_unitario'          => 'float',
        'precio'                   => 'float',
        'subtotal'                 => 'float',
        'aplica_iva'               => 'boolean',
        'iva_porcentaje'           => 'float',
        'otro_impuesto_porcentaje' => 'float',
        'impuesto_total'           => 'float',
        'traslado'                 => 'float',
        'retencion'                => 'float',
        'descuento'                => 'float',
        'isr'                      => 'float',
        'ilc'                      => 'float',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'factura_id');
    }
}

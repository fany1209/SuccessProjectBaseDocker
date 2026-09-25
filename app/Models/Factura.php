<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';
    protected $primaryKey = 'factura_id';

    protected $fillable = [
        'empresa',
        'tipo_documento',
        'folio_factura',
        'subtotal',
        'descuento_total',
        'iva',
        'traslado_total',
        'retencion_total',
        'total',
        'metodo_pago',
        'banco',
        'pagador',
        'terminacion',
        'descripcion',
        'departamento',
        'moneda',
        'tipo_cambio',
        'fecha_factura',
        'insumo',
    ];

    protected $casts = [
        'subtotal'        => 'float',
        'descuento_total' => 'float',
        'iva'             => 'float',
        'traslado_total'  => 'float',
        'retencion_total' => 'float',
        'total'           => 'float',
        'tipo_cambio'     => 'float',
        'fecha_factura'   => 'date',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaDetalle::class, 'factura_id', 'factura_id');
    }

    public function supplierPrices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class, 'factura_id', 'factura_id');
    }

    public function cxpDetail(): HasOne
    {
        return $this->hasOne(CxpDetail::class, 'factura_id', 'factura_id');
    }
}

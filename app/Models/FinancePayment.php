<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancePayment extends Model
{
    use HasFactory;

    protected $table = 'finance_payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'empresa',
        'cantidad',
        'motivo',
        'banco',
        'factura',
        'fecha_factura',
        'fecha_pago',
        'estatus',
        'comentarios',
        'semana',
        'anio',
        'terminacion',
        'efectivo',
    ];

    protected $casts = [
        'cantidad'      => 'float',
        'semana'        => 'integer',
        'anio'          => 'integer',
        'fecha_factura' => 'date',
        'fecha_pago'    => 'date',
    ];

    public function facturaRel(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'factura', 'factura_id');
    }
}

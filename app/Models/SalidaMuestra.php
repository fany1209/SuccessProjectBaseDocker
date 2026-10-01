<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalidaMuestra extends Model
{
    use HasFactory;

    protected $table = 'salida_muestras';

    protected $fillable = [
        'folio_muestra',
        'product_id',
        'fecha_salida',
        'nombre_comercial',
        'sku',
        'lote',
        'um',
        'cantidad',
        'descripcion',
        'motivo_salida',
        'motivo_otro',
        'entrega_paqueteria',
        'entrega_recoleccion_planta',
        'entrega_personal_empresa',
        'entrega_otro',
        'entrega_otro_txt',
        'paq_empresa',
        'paq_guia',
        'dest_nombre',
        'dest_direccion',
        'dest_recibe',
        'dest_correo',
        'dest_telefono',
        'docs_cc',
        'docs_ft',
        'docs_hs',
        'docs_otro',
        'docs_otro_txt',
    ];

    protected $casts = [
        'fecha_salida'               => 'date',
        'entrega_paqueteria'         => 'boolean',
        'entrega_recoleccion_planta' => 'boolean',
        'entrega_personal_empresa'   => 'boolean',
        'entrega_otro'               => 'boolean',
        'docs_cc'                    => 'boolean',
        'docs_ft'                    => 'boolean',
        'docs_hs'                    => 'boolean',
        'docs_otro'                  => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

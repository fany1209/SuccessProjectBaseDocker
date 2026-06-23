<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionOfSample extends Model
{
    use HasFactory;

    protected $table = 'reception_of_samples';

    protected $fillable = [
        'folio_muestra',
        'product_id',
        'nombre_comercial',
        'sku',
        'batch',
        'fecha_entrada',
        'fecha_caducidad',
        'descripcion',
        'origen_muestra',
        'origen_otro',
        'objetivo_muestra',
        'objetivo_otro',
        'cantidad',
        'um',
        'um_otro',
        'supplier_id',
        'docs_ccf',
        'docs_ft',
        'docs_hs',
        'docs_otro',
        'docs_otro_txt',
        'observaciones',
        'observaciones_laboratorio',
        'firma_entrega_nombre',
        'firma_recepcion_nombre',
        'estatus', 
    ];
}

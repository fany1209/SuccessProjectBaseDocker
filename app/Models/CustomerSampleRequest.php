<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSampleRequest extends Model
{
    protected $table = 'customer_sample_requests';

    protected $fillable = [
        'folio', 'fecha_solicitud', 'product_id', 'sku', 'um', 'cantidad',
        'pres_ziploc', 'pres_whirlpak', 'pres_metalizada', 'pres_frasco', 'pres_bidon', 'pres_otro', 'pres_otro_txt',
        'lote_almacen', 'lote_venta', 'fecha_recoleccion',
        'docs_cc', 'docs_ft', 'docs_hs', 'docs_otro', 'docs_otro_txt',
        'customer_id', 'cliente_nombre', 'cliente_direccion', 'cliente_correo', 'cliente_telefono', 'cliente_estatus', 'personal_seguimiento',
        'entrega_paqueteria', 'entrega_personal_empresa', 'entrega_recoleccion_planta', 'entrega_otro', 'entrega_otro_txt',
        'paq_nombre', 'paq_guia', 'observaciones', 'solicitante_nombre'
    ];
}
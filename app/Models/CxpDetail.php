<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxpDetail extends Model
{
    use HasFactory;

    protected $table = 'cxp_details';

    protected $fillable = [
        'factura_id',
        'fecha_pago',
        'semana',
        'anio',
        'estatus',
        'is_canceled',
        'pdf_path',
        'xml_path'
    ];

    public function payments()
    {
        return $this->hasMany(CxpPayment::class, 'cxp_detail_id', 'id');
    }

    public function factura()
    {
        return $this->belongsTo(\stdClass::class, 'factura_id', 'factura_id'); // We'll see if there is a Factura model
    }
}

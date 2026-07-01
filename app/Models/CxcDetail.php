<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxcDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'metodo_pago',
        'descripcion',
        'estatus',
        'fecha_conclusion',
        'is_canceled'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }

    public function payments()
    {
        return $this->hasMany(CxcPayment::class, 'cxc_detail_id');
    }
}

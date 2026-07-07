<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxpPayment extends Model
{
    use HasFactory;

    protected $table = 'cxp_payments';

    protected $fillable = [
        'cxp_detail_id',
        'amount',
        'date',
        'comprobante',
        'notas'
    ];

    public function detail()
    {
        return $this->belongsTo(CxpDetail::class, 'cxp_detail_id', 'id');
    }
}

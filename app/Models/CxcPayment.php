<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxcPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cxc_detail_id',
        'amount',
        'date',
        'comprobante',
    ];

    public function detail()
    {
        return $this->belongsTo(CxcDetail::class, 'cxc_detail_id');
    }
}

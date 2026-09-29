<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotRequest extends Model
{
    use HasFactory;

    protected $table = 'lot_requests';

    protected $fillable = [
        'department',
        'requested_at',
        'status',
        'lab_seen',
        'comments',
        'product',
        'quantity',
        'provider',
        'collector',
        'sector',
        'sku',
        'batch',
    ];
}

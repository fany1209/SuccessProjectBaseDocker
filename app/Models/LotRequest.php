<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotRequest extends Model
{
    protected $table = 'lot_requests';

    protected $fillable = [
        'requested_at',
        'department',
        'comments',
        'status',
    ];
}

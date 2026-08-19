<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mes_1',
        'mes_2',
        'mes_3',
        'indefinido',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

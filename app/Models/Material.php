<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'material_lab';

    protected $fillable = [
        'name',
        'entries',
        'exits',
        'stock',
        'um',
        'brand',
    ];

    protected $casts = [
        'entries' => 'float',
        'exits'   => 'float',
        'stock'   => 'float',
    ];
}

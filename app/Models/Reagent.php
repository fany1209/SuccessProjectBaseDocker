<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reagent extends Model
{
    use HasFactory;

    protected $table = 'reagent_inventory';

    protected $fillable = [
        'code',
        'name',
        'entries',
        'exits',
        'stock',
        'um',
        'brand',
        'color',
    ];

    protected $casts = [
        'entries' => 'float',
        'exits'   => 'float',
        'stock'   => 'float',
    ];
}

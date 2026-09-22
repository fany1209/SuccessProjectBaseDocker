<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FertilProduction extends Model
{
    use HasFactory;

    protected $primaryKey = 'fertil_production_id';

    protected $fillable = [
        'fecha_preparacion',
        'kg_preparados',
        'fecha_ensacado',
        'kg_ensacados',
        'num_sacos',
        'descripcion'
    ];
}

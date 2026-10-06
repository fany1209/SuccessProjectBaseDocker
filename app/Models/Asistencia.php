<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'nombre',
        'fecha',
        'entrada',
        'salida_comida',
        'regreso_comida',
        'salida_final',
        'tipo',
        'comentario',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}

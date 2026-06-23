<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fumigacion extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención en plural)
    protected $table = 'fumigaciones';

    /**
     * Los atributos que se pueden asignar masivamente.
     * Esto evita el error de "MassAssignmentException".
     */
    protected $fillable = [
        'proveedor',
        'fecha_programada',
        'metodo_aplicacion',
        'estado',
        'observaciones',
    ];

    /**
     * Cast de tipos. 
     * Al poner fecha_programada como 'datetime', Laravel te permite 
     * usar Carbon directamente en la vista: $fumigacion->fecha_programada->format('d/m/Y')
     */
    protected $casts = [
        'fecha_programada' => 'datetime',
    ];
}
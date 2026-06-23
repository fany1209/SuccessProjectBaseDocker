<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionW extends Model
{
    protected $table = 'inspections_w';
    protected $fillable = [
        'fecha_inspeccion',
        'inspector',
        'hora_turno',
        'turno',
        'area',
        'area_otro',
        'responsable',
        'comentarios_q',
    ];
    protected $casts = [
        'area' => 'array',
    ];

    public function observaciones()
    {
        return $this->hasMany(\App\Models\Observacion::class, 'inspection_id', 'id');
    }
}

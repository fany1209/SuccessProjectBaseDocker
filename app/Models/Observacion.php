<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'observaciones';

    protected $fillable = [
        'inspection_id', 'name', 'rev', 'fecha', 'ubicacion', 'evidencia_path', 'ev_corr_path',
    ];

    public function inspection()
    {
        return $this->belongsTo(\App\Models\InspectionW::class, 'inspection_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClimaLaboral extends Model
{
    use HasFactory;

    protected $table = 'clima_laborales';

    protected $fillable = [
        'user_id',
        'q1_ambiente',
        'q2_respeto',
        'q3_comunicacion_oportuna',
        'q4_comunicacion_escucha',
        'q5_liderazgo',
        'q6_reconocimiento',
        'q7_desarrollo',
        'q8_motivacion',
        'q9_satisfaccion',
        'q10_bienestar_carga',
        'q11_bienestar_preocupacion',
        'q12_sugerencias',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

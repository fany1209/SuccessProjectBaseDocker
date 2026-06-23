<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Minuta extends Model
{
    use HasFactory;

    protected $table = 'minutas';

    protected $primaryKey = 'id_minuta';

    protected $fillable = [
        'user_id',
        'fecha_hora',
        'lugar',
        'tema_general',
        'ponente',
        'asistente_nombre',
        'asistente_departamento',
        'tema_tratado',
        'acuerdo',
        'responsable',
        'fecha_compromiso',
        'fecha_cierre',
        'estatus'
    ];

    public $timestamps = false; 

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
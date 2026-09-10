<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';

    protected $fillable = [
        'nombre',
        'curp',
        'rfc',
        'nss',
        'puesto',
        'fecha_ingreso',
        'fecha_baja',
        'edad',
        'antiguedad',
        'sexo',
        'estado_civil',
        'fecha_nacimiento',
        'nombre_beneficiario',
        'parentesco',
        'domicilio',
        'cp',
        'telefono',
        'correo',
        'estatus',
        'user_id',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date:Y-m-d',
        'fecha_baja' => 'date:Y-m-d',
        'fecha_nacimiento' => 'date:Y-m-d',
    ];

    protected $appends = [
        'calculated_edad',
        'calculated_antiguedad',
    ];

    /**
     * Calcula la edad automáticamente a partir de la fecha de nacimiento.
     */
    public function getCalculatedEdadAttribute(): ?int
    {
        if ($this->fecha_nacimiento) {
            return Carbon::parse($this->fecha_nacimiento)->age;
        }
        return $this->attributes['edad'] ?? null;
    }

    /**
     * Calcula la antigüedad automáticamente desde la fecha de ingreso
     * hasta la fecha de baja (o la fecha actual si no hay baja).
     */
    public function getCalculatedAntiguedadAttribute(): ?string
    {
        if (!$this->fecha_ingreso) {
            return $this->attributes['antiguedad'] ?? '—';
        }

        $inicio = Carbon::parse($this->fecha_ingreso)->startOfDay();
        $fin = $this->fecha_baja ? Carbon::parse($this->fecha_baja)->startOfDay() : Carbon::now()->startOfDay();

        if ($inicio->gt($fin)) {
            return '0 días';
        }

        $diff = $inicio->diff($fin);
        $partes = [];

        if ($diff->y > 0) {
            $partes[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
        }
        if ($diff->m > 0) {
            $partes[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
        }
        if (empty($partes)) {
            $partes[] = $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
        }

        return implode(', ', $partes);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

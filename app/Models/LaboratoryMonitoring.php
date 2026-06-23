<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaboratoryMonitoring extends Model
{
    use SoftDeletes;

    protected $table = 'laboratory_monitoring';

    protected $fillable = [
        'asesor','productor_id','cultivo','folio','producto_aplicar',
        'peso_text', 
        'objetivo','condiciones',
        'ubicacion','ubicacion_nombre',
        'division_bloques','tratamiento','dosis',
        'fecha_aplicacion','doc_muestreo','fechas_muestreo',
        'variables_agro','observaciones',
    ];

    protected $casts = [
        'dosis'            => 'array',
        'fechas_muestreo'  => 'array',
        'fecha_aplicacion' => 'date',
    ];

    public function productor()
    {
        return $this->belongsTo(Customer::class, 'productor_id', 'customer_id');
    }
}

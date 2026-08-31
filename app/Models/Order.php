<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     * * @var string
     */
    protected $table = 'orders';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     * * @var array
     */
    protected $fillable = [
        'user_id',
        'año', 'semana', 'empresa', 'cantidad', 'producto','pdf_path', 'po', 
        'fecha_de_carga', 'hora', 'fecha_de_envio', 
        'fecha_requerida_por_el_cliente', 'transporte', 
        'estatus_almacen', 'estatus_calidad', 'estatus_administrativo', 
        'documentacion_requerida', 'comentarios'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
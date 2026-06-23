<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';
    protected $fillable = [
        'fecha',
        'tipo',
        'motivos',
        'motivo_otro',
        'descripcion',
        'evidencias',
    ];

    protected $casts = [
        'fecha'      => 'date',
        'motivos'    => 'array',
        'evidencias' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryEquipment extends Model
{
    use HasFactory;

    protected $table = 'laboratory_equipments';

    protected $fillable = [
        'internal_code',
        'name',
        'quantity',
        'brand',
        'status',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];
}

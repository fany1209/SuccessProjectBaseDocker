<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItEquipment extends Model
{
    use HasFactory;

    protected $table = 'it_equipments';

    protected $fillable = [
        'department',
        'responsible',
        'article',
        'brand',
        'model',
        'serial_number',
        'success_code',
        'image_url',
    ];
}

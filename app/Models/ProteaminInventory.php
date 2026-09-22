<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProteaminInventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'proteamin_inventory_id';

    protected $fillable = [
        'producto_descripcion',
        'cantidad',
        'unidad',
        'stock_min'
    ];
}

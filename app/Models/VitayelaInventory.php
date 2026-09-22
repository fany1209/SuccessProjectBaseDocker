<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitayelaInventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'vitayela_inventory_id';

    protected $fillable = [
        'producto_descripcion',
        'cantidad',
        'unidad',
        'stock_min'
    ];
}

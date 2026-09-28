<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FertilInventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'fertil_inventory_id';

    protected $fillable = [
        'producto_descripcion',
        'cantidad',
        'unidad',
        'stock_min',
    ];

    protected $casts = [
        'cantidad'  => 'float',
        'stock_min' => 'float',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(FertilInventoryMovement::class, 'fertil_inventory_id', 'fertil_inventory_id');
    }
}

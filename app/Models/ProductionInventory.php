<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionInventory extends Model
{
    use HasFactory;

    protected $table = 'production_inventories';
    protected $primaryKey = 'production_inventory_id';

    protected $fillable = [
        'area',
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
        return $this->hasMany(ProductionInventoryMovement::class, 'production_inventory_id', 'production_inventory_id');
    }
}


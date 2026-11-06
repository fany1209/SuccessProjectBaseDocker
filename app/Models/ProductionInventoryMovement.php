<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionInventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'production_inventory_movements';
    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'production_inventory_id',
        'tipo',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'float',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(ProductionInventory::class, 'production_inventory_id', 'production_inventory_id');
    }
}


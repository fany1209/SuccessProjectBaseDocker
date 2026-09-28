<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FertilInventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'fertil_inventory_movements';

    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'fertil_inventory_id',
        'tipo',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'float',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(FertilInventory::class, 'fertil_inventory_id', 'fertil_inventory_id');
    }
}

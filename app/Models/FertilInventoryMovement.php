<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function inventory()
    {
        return $this->belongsTo(FertilInventory::class, 'fertil_inventory_id', 'fertil_inventory_id');
    }
}

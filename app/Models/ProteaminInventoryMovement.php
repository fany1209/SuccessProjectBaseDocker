<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProteaminInventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'proteamin_inventory_movements';

    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'proteamin_inventory_id',
        'tipo',
        'cantidad',
    ];

    public function inventory()
    {
        return $this->belongsTo(ProteaminInventory::class, 'proteamin_inventory_id', 'proteamin_inventory_id');
    }
}

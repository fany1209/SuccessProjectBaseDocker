<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitayelaInventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'vitayela_inventory_movements';

    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'vitayela_inventory_id',
        'tipo',
        'cantidad',
    ];

    public function inventory()
    {
        return $this->belongsTo(VitayelaInventory::class, 'vitayela_inventory_id', 'vitayela_inventory_id');
    }
}

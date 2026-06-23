<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarantine extends Model
{
    use HasFactory;

    protected $table = 'quarantine';

    protected $primaryKey = 'quarantine_id';

    protected $fillable = ['quantity', 'notes', 'inventory_id'];

    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory', 'inventory_id');
    }
}

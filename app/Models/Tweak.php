<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweak extends Model
{
    use HasFactory;

    protected $primaryKey = 'tweak_id';

    protected $fillable = ['type', 'quantity', 'comments', 'inventory_id'];

    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory', 'inventory_id');
    }
}

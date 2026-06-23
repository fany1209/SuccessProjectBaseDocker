<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInputs extends Model
{
    use HasFactory;

    protected $table = 'product_inputs';

    protected $fillable = ['quantity', 'warehouse_batch'];

    public $timestamps = false;
}

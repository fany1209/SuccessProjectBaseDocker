<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOutputs extends Model
{
    use HasFactory;

    protected $table = 'product_outputs';

    protected $fillable = ['output_id', 'product_id', 'quantity', 'warehouse_batch', 'label_batch'];

    public $timestamps = false;
}

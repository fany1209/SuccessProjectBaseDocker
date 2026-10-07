<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionWarehouseTransfer extends Model
{
    use HasFactory;

    protected $table = 'production_warehouse_transfers';
    protected $primaryKey = 'transfer_id';

    protected $fillable = [
        'area',
        'production_id',
        'product_id',
        'product_name',
        'unit_type',
        'quantity',
        'weight_per_unit',
        'total_weight',
        'batch',
        'status',
        'created_by',
        'received_by',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

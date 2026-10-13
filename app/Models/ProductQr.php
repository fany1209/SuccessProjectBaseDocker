<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQr extends Model
{
    use HasFactory;

    protected $table = 'product_qrs';

    protected $fillable = [
        'uuid',
        'cli_id',
        'inventory_id',
        'product_id',
        'location_id',
        'concept_id',
        'bag_number',
        'batch',
        'quantity',
        'weight_per_unit',
        'net_weight',
        'unit',
        'description',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'weight_per_unit' => 'decimal:4',
        'net_weight' => 'decimal:4',
    ];

    public function cli(): BelongsTo
    {
        return $this->belongsTo(Cli::class, 'cli_id', 'cli_id');
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class, 'concept_id', 'concept_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getLiveNetWeightAttribute(): float
    {
        return $this->cli ? (float) $this->cli->net_weight : (float) $this->net_weight;
    }

    public function getLiveQuantityAttribute(): float
    {
        return $this->cli ? (float) $this->cli->quantity : (float) $this->quantity;
    }

    public function getIsActiveInRackAttribute(): bool
    {
        return !is_null($this->cli) && $this->cli->exists && $this->cli->net_weight > 0;
    }
}

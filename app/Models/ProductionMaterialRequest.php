<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterialRequest extends Model
{
    use HasFactory;

    protected $table = 'production_material_requests';

    protected $fillable = [
        'area',
        'applicant_name',
        'status',
        'comments'
    ];

    public function items()
    {
        return $this->hasMany(ProductionMaterialRequestItem::class, 'request_id');
    }
}

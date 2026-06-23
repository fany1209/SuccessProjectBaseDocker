<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'supplier',
        'url',
        'use',
        'quantity',
        'image_url',
        'pr_id'
    ];

    public function requisition()
    {
        return $this->belongsTo('App\Models\PurchaseRequisition', 'pr_id');
    }
}

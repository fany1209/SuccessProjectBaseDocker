<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $table = 'purchases_requisitions';

    protected $fillable = [
        'consecutive',
        'purchase_order',
        'applicant',
        'department',
        'data_sheet',
        'safety_sheet',
        'comparative_id',
    ];

    public function products()
    {
        return $this->hasMany('App\Models\RequisitionProduct', 'pr_id');
    }

    public function comparative()
    {
        return $this->belongsTo(Comparative::class, 'comparative_id');
    }
}

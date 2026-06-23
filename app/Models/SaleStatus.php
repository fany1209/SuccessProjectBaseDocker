<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sales_status';

    protected $primaryKey = 'sales_status_id';

    protected $fillable = ['name', 'description'];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'sales_status_id');
    }
}

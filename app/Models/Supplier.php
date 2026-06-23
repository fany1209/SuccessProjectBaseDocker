<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'supplier_code',
        'sector_id',
        'name',
        'contact',
        'phone',
        'email',
        'rfc',
        'state',
        'city',
        'district',
        'address',
    ];

    public function inputs()
    {
        return $this->hasMany('App\Models\Input', 'supplier_id');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector', 'sector_id');
    }
}

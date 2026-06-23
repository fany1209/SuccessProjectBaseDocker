<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
            'name',
            'contact',          
            'vendedor',         
            'phone',
            'email',
            'rfc',
            'postal_code',
            'address',         
            'delivery_address',
            'district',
            'city',
            'state',
            'country',
            'customer_code',
            'sector_id',
        ];

    public function outputs()
    {
        return $this->hasMany('App\Models\Output', 'customer_id');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector', 'sector_id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'customer_id');
    }
}

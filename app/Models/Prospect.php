<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $primaryKey = 'prospect_id';

    protected $fillable = ['name', 'phone', 'email', 'rfc', 'district', 'city', 'state', 'address', 'sector_id'];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'prospect_id');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector', 'sector_id');
    }
}

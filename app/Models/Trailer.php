<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{
    use HasFactory;

    protected $primaryKey = 'trailer_id';

    protected $fillable = ['type', 'unit_number', 'plate', 'color', 'transport_line_id'];

    public function transportLine()
    {
        return $this->belongsTo('App\Models\TransportLine','transport_line_id');
    }
}

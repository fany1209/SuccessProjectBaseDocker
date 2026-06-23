<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportLine extends Model
{
    use HasFactory;

    protected $table = 'transport_lines';

    protected $primaryKey = 'transport_line_id';

    protected $fillable = ['name'];

    public function vehicles()
    {
        return $this->hasMany('App\Models\Vehicle', 'transport_line_id');
    }

    public function trailers()
    {
        return $this->hasMany('App\Models\Trailer', 'transport_line_id');
    }

    public function inputs()
    {
        return $this->hasMany('App\Models\Input', 'transport_line_id');
    }

    public function outputs()
    {
        return $this->hasMany('App\Models\Output', 'transport_line_id');
    }
}

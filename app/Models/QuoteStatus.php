<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'quotes_status';

    protected $primaryKey = 'quotes_status_id';

    protected $fillable = ['name', 'description'];

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote', 'quotes_status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concept extends Model
{
    use HasFactory;

    protected $primaryKey = 'concept_id';

    protected $fillable = ['name'];

    public function clis()
    {
        return $this->hasMany('App\Models\Cli', 'concept_id');
    }
}

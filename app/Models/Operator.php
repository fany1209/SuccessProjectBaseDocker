<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $table = 'operators';

    protected $primaryKey = 'operator_id';

    protected $fillable = ['name','license'];
}

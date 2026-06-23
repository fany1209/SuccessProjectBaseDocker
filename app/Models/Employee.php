<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';

    protected $fillable = ['name', 'last_name', 'birth_date', 'phone', 'rfc', 'curp', 'district', 'city', 'state', 'address'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'employee_id', 'employee_id');
    }
}

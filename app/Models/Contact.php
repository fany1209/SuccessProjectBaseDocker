<?php
/*
Fecha de actualización: 17/12/2025
Actualizado por Jacob
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $primaryKey = 'contact_id';

    protected $fillable = ['name', 'phone', 'message', 'email', 'read_at'];
}

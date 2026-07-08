<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PortalUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'portal_users';

    protected $fillable = [
        'nombre_contacto', 'empresa', 'email', 'password', 'is_active', 'referencia_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function customer()
{
    return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
}

}
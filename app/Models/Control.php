<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use HasFactory;

    protected $table = 'control';

    protected $primaryKey = 'control_id';

    protected $fillable = ['temperature', 'humidity', 'host_ip', 'host_user', 'host_name', 'warehouse_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    }
}

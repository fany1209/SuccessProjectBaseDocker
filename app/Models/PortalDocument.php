<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalDocument extends Model
{
    protected $table = 'portal_documents';

    protected $fillable = [
        'sale_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'date',
        'brand',
        'model',
        'serial_number',
        'location',
        'area',
        'req1',
        'req2',
        'req3',
        'req4',
        'req5',
        'observations',
        'inspector_name',
    ];

    public static function generateFolio()
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'INS-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}

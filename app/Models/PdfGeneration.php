<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfGeneration extends Model
{
    protected $table = 'pdf_clicks'; 
    public $timestamps = false; 

    protected $fillable = [
        'reference_id',
        'pdf_type',
        'user_id',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];
}

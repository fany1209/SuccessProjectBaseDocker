<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDirectory extends Model
{
    use HasFactory;

    protected $table = 'supplier_directory';
    protected $primaryKey = 'Code_supplier';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'Code_supplier',
        'Name',
        'Product',
        'Address',
        'Phone',
        'Email',
        'RFC',
        'Contact',
    ];
}

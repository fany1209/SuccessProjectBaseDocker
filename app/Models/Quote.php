<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $primaryKey = 'quote_id';

    protected $fillable = [
        'folio', 'company','date', 'attention', 'phone', 'email', 'department', 'place_of_delivery', 'transport_specification',
        'deadline', 'terms', 'notes', 'quotes_status_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'quote_detail', 'quote_id', 'product_id')->withPivot('quote_product_name', 'quantity', 'cost', 'presentation');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\QuoteStatus', 'quotes_status_id');
    }

    public function details()
    {
        return $this->hasMany(QuoteDetail::class, 'quote_id', 'quote_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }
    
    protected $casts = [
        'deadline' => 'date', 
    ];
}

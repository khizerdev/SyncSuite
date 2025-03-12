<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
    ];
    
    
     public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
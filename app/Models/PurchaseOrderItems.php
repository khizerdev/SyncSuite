<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class PurchaseOrderItems extends Model {
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    protected $table = "purchase_order_items";

    
    public function product()
    {
        return $this->belongsTo('App\Models\Product','product_id');
    }
    
     public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase','purchase_id');
    }
    

}
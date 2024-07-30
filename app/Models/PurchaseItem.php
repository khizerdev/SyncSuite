<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class PurchaseItem extends Model {
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product','product_id');
    }
    

     public function invoice()
    {
        return $this->hasOne('App\Models\PurchaseInvoiceItems','receipt_item_id');
    }
    
     public function receipt()
    {
        return $this->belongsTo('App\Models\PurchaseReceipt','receipt_id');
    }
    

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class PurchaseInvoiceItems extends Model {
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    protected $table = "purchase_invoice_items";

    
    public function receipt()
    {
        return $this->belongsTo('App\Models\PurchaseItem','receipt_item_id');
    }
    
     public function invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice','invoice_id');
    }
    

}
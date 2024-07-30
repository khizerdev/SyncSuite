<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseReceipt extends Model {
    
    protected $table = "purchase_receipts";
    protected $guarded = [];
    protected $dates = [
      'date',
      'due_date',
      'created_at',
      'updated_at',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];


    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase','purchase_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\PurchaseItem','receipt_id');
    }
    
    
    
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {

    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    public function items()
    {
        return $this->hasMany('App\Models\PurchaseOrderItems','purchase_id');
    }

    public function invoice()
    {
        return $this->hasOne('App\PurchaseInvoice');
    }

    public function receipt()
    {
        return $this->hasOne('App\Models\PurchaseReceipt','Purchase_id');
    }

}
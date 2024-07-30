<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class PurchaseInvoice extends Model {
    

    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
         'due_date',
         'date'

    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany('App\Models\PurchaseInvoiceItems','invoice_id');
    }

}
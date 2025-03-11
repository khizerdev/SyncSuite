<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerReceivable extends Model
{
 
    protected $table = "customers_receiveables";
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'cheque_date',
    ];
    
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer','customer_id');
    }
    
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor','receiver_id');
    }
    
    public function account()
    {
        return $this->belongsTo('App\Models\Account','receiver_id');
    }
  
}
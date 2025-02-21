<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VendorPayable extends Model
{
    
    protected $table = "vendors_payables";
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'cheque_date',
    ];
    
    
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor','vendor_id');
    }
    
    public function account()
    {
        return $this->belongsTo('App\Models\Account','account_id');
    }

}
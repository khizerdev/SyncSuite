<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAdjustment extends Model
{
    protected $table = "customer_adjustments";
    protected $guarded = [];
    protected $dates = [
      'created_at',
      'updated_at',
      'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
}
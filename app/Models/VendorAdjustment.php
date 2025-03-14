<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class VendorAdjustment extends Model
{
    protected $table = "vendor_adjustments";
    protected $guarded = [];
    protected $dates = [
      'created_at',
      'updated_at',
      'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
}
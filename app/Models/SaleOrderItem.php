<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function design()
    {
        return $this->belongsTo(FabricMeasurement::class);
    }
    public function color()
    {
        return $this->belongsTo(ColorCode::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
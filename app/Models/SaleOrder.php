<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function designs()
    {
        return $this->belongsToMany(FabricMeasurement::class, 'sale_orders', 'sale_order_id', 'design_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }
}
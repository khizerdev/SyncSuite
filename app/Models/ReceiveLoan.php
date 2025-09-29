<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sale_order_id',
        'date',
        'descr'
    ];

    public function productSaleOrder()
    {
        return $this->belongsTo(ProductSaleOrder::class);
    }

    public function items()
    {
        return $this->hasMany(ReceiveLoanItem::class);
    }
}
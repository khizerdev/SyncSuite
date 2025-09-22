<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSaleOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sale_order_id',
        'product_id',
        'qty',
        'rate',
        'dispatched_through'
    ];

    protected $table = 'product_sale_order_items';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleOrder()
    {
        return $this->belongsTo(ProductSaleOrder::class);
    }
}
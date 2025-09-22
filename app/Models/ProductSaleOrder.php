<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSaleOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'serial_no',
        'serial',
        'date',
        'descr'
    ];
    
    protected $casts = [
        'date' => 'date', // This will automatically cast the date field to a Carbon instance
    ];

    protected $table = 'product_sale_orders';

    public function items(): HasMany
    {
        return $this->hasMany(ProductSaleOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
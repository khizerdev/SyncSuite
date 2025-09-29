<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveLoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_loan_id',
        'product_id',
        'qty',
        'rate',
        'dispatched_through'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function receiveLoan()
    {
        return $this->belongsTo(ReceiveLoan::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProductionItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dailyProduction()
    {
        return $this->belongsTo(DailyProduction::class);
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function saleOrderItem()
    {
        return $this->belongsTo(SaleOrderItem::class);
    }
    
    public function thanIssueItems()
    {
        return $this->hasMany(ThanIssueItem::class);
    }
}
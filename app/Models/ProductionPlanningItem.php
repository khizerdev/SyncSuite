<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPlanningItem extends Model
{
    protected $fillable = [
        'production_planning_id',
        'sale_order_item_id',
        'planned_qty',
        'planned_lace_qty',
        'produced_qty',
        'produced_lace_qty',
        'status'
    ];

    public function productionPlanning()
    {
        return $this->belongsTo(ProductionPlanning::class);
    }

    public function saleOrderItem()
    {
        return $this->belongsTo(SaleOrderItem::class);
    }

    // Calculate completion percentage for this specific item
    public function getCompletionPercentageAttribute()
    {
        if ($this->planned_qty == 0) return 0;
        return round(($this->produced_qty / $this->planned_qty) * 100, 2);
    }
}
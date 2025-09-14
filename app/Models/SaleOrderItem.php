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
    
    public function productionPlannings()
    {
        return $this->belongsToMany(ProductionPlanning::class, 'production_planning_items')
                    ->withPivot('planned_qty', 'planned_lace_qty', 'produced_qty', 'produced_lace_qty', 'status')
                    ->withTimestamps();
    }

    public function planningItems()
    {
        return $this->hasMany(ProductionPlanningItem::class);
    }
}
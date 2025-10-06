<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPlanning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleOrder(){
        return $this->belongsTo(SaleOrder::class);
    }
    
    public function machine(){
        return $this->belongsTo(Machine::class);
    }
    
    // Get all items linked to this production planning
    public function items()
    {
        return $this->belongsToMany(SaleOrderItem::class, 'production_planning_items')
                    ->withPivot('planned_qty', 'planned_lace_qty')
                    ->withTimestamps();
    }

    // Get the pivot records directly
    public function planningItems()
    {
        return $this->hasMany(ProductionPlanningItem::class);
    }

    // Calculate completion percentage
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_planned_qty == 0) return 0;
        
        $totalProduced = $this->planningItems()->sum('produced_qty');
        return round(($totalProduced / $this->total_planned_qty) * 100, 2);
    }
    
}

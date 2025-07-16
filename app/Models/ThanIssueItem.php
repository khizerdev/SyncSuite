<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanIssueItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $prefix = strtoupper(substr($model->productGroup->code, 0, 2)); // Get first 2 letters of product code
            $latest = ThanIssueItem::where('serial_no', 'like', $prefix . '-%')
                         ->where('product_group_id', $model->product_group_id)
                         ->orderBy('id', 'DESC')
                         ->first();
            
            $nextNumber = $latest ? (int) substr($latest->serial_no, 3) + 1 : 1;
            $model->serial_no = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public function thanIssue()
    {
        return $this->belongsTo(ThanIssue::class);
    }

    public function dailyProductionItem()
    {
        return $this->belongsTo(DailyProductionItem::class);
    }

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }
    
    public function fabricMeasurements()
{
    return $this->belongsToMany(FabricMeasurement::class, 'design_than_issue_item', 
        'than_issue_item_id', // Foreign key on the pivot table for ThanIssueItem
        'fabric_measurement_id' // Foreign key on the pivot table for FabricMeasurement
    );
}
}
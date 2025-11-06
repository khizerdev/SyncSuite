<?php

namespace App\Models;

use App\Models\Machine;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;

class DailyProduction extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($dailyProduction) {
            $previousRecord = DailyProduction::where('machine_id', $dailyProduction->machine_id)
                ->latest()
                ->first();
            
            $dailyProduction->previous_stitch = $previousRecord ? $previousRecord->actual_stitch : 0;
            $dailyProduction->actual_stitch = $dailyProduction->current_stitch - $dailyProduction->previous_stitch;
        });

        static::updating(function ($dailyProduction) {
            $dailyProduction->actual_stitch = $dailyProduction->current_stitch - $dailyProduction->previous_stitch;
        });
    }

    public function shift()
    {
        return $this->belongsTo(ProductionShift::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function items()
    {
        return $this->hasMany(DailyProductionItem::class);
    }
}
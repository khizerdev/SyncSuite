<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanSupply extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = ThanSupply::orderBy('id', 'DESC')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $model->serial_no = 'SL-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function items()
    {
        return $this->hasMany(ThanSupplyItem::class, "daily_production_id");
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
    
    public function receipts()
    {
        return $this->hasMany(SupplyReceipt::class, 'than_supply_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanSupplyItem extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function thanSupply()
    {
        return $this->belongsTo(ThanSupply::class);
    }
    
    public function batchItems()
    {
        return $this->hasMany(BatchItem::class, 'than_supply_item_id');
    }

  
}
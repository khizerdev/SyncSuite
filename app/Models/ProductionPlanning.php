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
    
}

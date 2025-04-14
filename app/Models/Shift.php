<?php

namespace App\Models;

use App\DailyProduction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dailyProductions()
    {
        return $this->hasMany(DailyProduction::class);
    }

}

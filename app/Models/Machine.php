<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'code',
        'manufacturer_id',
        'name',
        'number',
        'purchased_date',
        'model_date',
        'capacity',
        'production_speed',
        'price',
        'warranty',
        'attachments',
        'remarks',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function dailyProductions()
    {
        return $this->hasMany(DailyProduction::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_of_measure',
        'design_stitch',
        'front_yarn',
        'back_yarn',
    ];
}
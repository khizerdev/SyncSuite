<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftTransfer extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
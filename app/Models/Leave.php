<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = ['employee_id', 'date', 'notes'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
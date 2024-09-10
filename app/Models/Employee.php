<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function timings()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function type()
    {
        return $this->belongsTo(EmployeeType::class, 'type_id');
    }
}

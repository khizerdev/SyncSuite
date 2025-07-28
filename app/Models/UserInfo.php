<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    
    public function employee()
    {
        return $this->hasOne(Employee::class, 'code', 'code');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'code', 'id');
    }
}

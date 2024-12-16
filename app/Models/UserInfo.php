<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'code', 'id');
    }

}

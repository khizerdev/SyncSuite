<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['title'];
    
    public function subDepartments()
    {
        return $this->hasMany(SubErpDepartment::class ,'department_id');
    }
}
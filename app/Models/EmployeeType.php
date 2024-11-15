<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    protected $table = 'employee_types';

    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'type_id');
    }

}

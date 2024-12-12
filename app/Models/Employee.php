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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function advanceSalaries()
    {
        return $this->hasMany(AdvanceSalary::class);

    }

    public function loans()
    {
        return $this->hasMany(Loan::class);

    }

    public function loan()
    {
        return $this->hasOne(Loan::class);

    }

    public function loanExceptions()
    {
        return $this->hasMany(LoanException::class);

    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'contact_number', 'address'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
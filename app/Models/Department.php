<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    
    public function products()
{
    return $this->belongsToMany(Product::class, 'inventory_department')
                ->withPivot('quantity')
                ->withTimestamps();
}
}

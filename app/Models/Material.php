<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'particular_id',
    ];

    public function particular()
    {
        return $this->belongsTo(Particular::class);
    }
}
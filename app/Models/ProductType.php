<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'material_id',
        'particular_id',
    ];


    public function material()
    {
        return $this->belongsTo(Material::class);
    }


    public function particular()
    {
        return $this->belongsTo(Particular::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'product_type_id',
        'material_id',
        'particular_id',
        'qty',
        'inventory_price',
        'total_price',
        'min_qty_limit',
    ];

    public function Department()
    {
        return $this->belongsTo(Department::class);
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function particular()
    {
        return $this->belongsTo(Particular::class);
    }


}

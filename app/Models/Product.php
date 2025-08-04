<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [ ];

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
    
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItems::class);
    }
    
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'inventory_department')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Add this accessor to easily get Main department stock
    public function getMainDepartmentStockAttribute()
    {
        return $this->departments()
            ->where('name', 'Main')
            ->first()->pivot->quantity ?? 0;
    }


}

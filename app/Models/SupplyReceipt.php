<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyReceipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'received_date' => 'date',
    ];

    /**
     * Get the than_supply that this receipt belongs to.
     */
    public function thanSupply()
    {
        return $this->belongsTo(ThanSupply::class, 'than_supply_id');
    }

    /**
     * Get the department that received the supply.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
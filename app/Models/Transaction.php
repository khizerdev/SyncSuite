<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date', // Add this line
    ];
    
    public function sender()
    {
        return $this->belongsTo(Account::class,'sender_id');
    }
    
    public function receiver()
    {
        return $this->belongsTo(Account::class,'receiver_id');
    }

}
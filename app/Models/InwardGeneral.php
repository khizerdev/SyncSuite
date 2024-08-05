<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class InwardGeneral extends Model {
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany('App\Models\InwardGeneralItem','inward_id');
    }
}
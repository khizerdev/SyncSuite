<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class InwardGeneralItem extends Model {
    
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function inward()
    {
        return $this->belongsTo('App\Models\InwardGeneral','inward_id');
    }
    

}
<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'date'
    ];

    public function account()
    {
        return $this->hasOne('App\Account','account_id');
    }

}
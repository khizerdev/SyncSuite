<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountAdjustment extends Model
{
    protected $table = "account_adjustments";
    protected $guarded = [];
    protected $dates = [
      'created_at',
      'updated_at',
      'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }
}
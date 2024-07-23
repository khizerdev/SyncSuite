<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'country',
        'city',
        'telephone',
        'res',
        'fax',
        's_man',
        'mobile',
        'strn',
        'ntn',
        'date',
        'balance_type',
        'opening_balance',
    ];
}

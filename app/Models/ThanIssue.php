<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanIssue extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = ThanIssue::orderBy('id', 'DESC')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $model->serial_no = 'SL-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function items()
    {
        return $this->hasMany(ThanIssueItem::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
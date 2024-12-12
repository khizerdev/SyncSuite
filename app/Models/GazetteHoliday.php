<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GazetteHoliday extends Model
{
    protected $guarded = [];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function hasHolidaysForMonth($year, $month)
    {
        return self::whereYear('holiday_date', $year)
            ->whereMonth('holiday_date', $month)
            ->exists();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\GazetteHoliday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GazetteController extends Controller
{

    public function getHolidays(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $holidays = GazetteHoliday::get()
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->holiday_date->format('Y-m-d'),
                ];
            });

        return response()->json($holidays);
    }

    public function store(Request $request)
    {
        $request->validate([
            'holidays' => 'required|array',
            'holidays.*.date' => 'required|date',
        ]);
        GazetteHoliday::query()->delete();
        foreach ($request->holidays as $holiday) {
            GazetteHoliday::create([
                'holiday_date' => $holiday['date'],
            ]);
        }

        return response()->json(['message' => 'Holidays saved successfully']);
    }

    public function checkMonthlyHolidays($year, $month)
    {
        $exists = GazetteHoliday::hasHolidaysForMonth($year, $month);
        return response()->json(['exists' => $exists]);
    }
}
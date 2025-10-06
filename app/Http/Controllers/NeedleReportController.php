<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyProduction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class NeedleReportController extends Controller
{
    public function index()
    {
        $machines = \App\Models\Machine::select('id', 'code')->get();
        return view('pages.reports.needle', compact('machines'));
    }

    public function report(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $productions = DailyProduction::with('items')
            ->where('machine_id', $request->machine_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        // Group totals by date
        $dailyTotals = $productions->groupBy('date')->map(function ($dayGroup) {
            return $dayGroup->flatMap->items->sum('needle');
        });

        // Fill missing dates with 0
        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        $completeData = collect();
        foreach ($period as $date) {
            $completeData[$date->toDateString()] = $dailyTotals[$date->toDateString()] ?? 0;
        }

        // Summary
        $totalNeedles = $completeData->sum();
        $averageNeedles = $completeData->avg() ?? 0;
        $maxNeedles = $completeData->max() ?? 0;
        $minNeedles = $completeData->min() ?? 0;
        $productionDays = $completeData->count();

        return response()->json([
            'dailyTotals' => $completeData,
            'summary' => [
                'totalNeedles' => $totalNeedles,
                'averageNeedles' => round($averageNeedles, 2),
                'maxNeedles' => $maxNeedles,
                'minNeedles' => $minNeedles,
                'productionDays' => $productionDays,
            ],
        ]);
    }
}

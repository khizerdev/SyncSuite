<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttendanceService;
use App\Models\Employee;
use App\Models\Missscan;

class MissScanController extends Controller
{
    protected $attendanceService;

    public function index(Request $request)
    {
        $employees = Employee::all();
        
        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));
        
        // Fetch resolved entries with duration
        $resolvedEntries = MissScan::where('month', $selectedMonth)
                                    ->where('year', $selectedYear)
                                    ->get(['employee_id', 'duration']);

        // Map resolved entries to composite keys (e.g., "employee_id:first_half")
        $resolvedEmployeeIds = $resolvedEntries->map(function ($entry) {
            return $entry->duration 
                ? $entry->employee_id . ':' . $entry->duration 
                : $entry->employee_id;
        })->toArray();

        // Process miss-scan data
        $missScanData = $employees->map(function ($employee) use ($selectedMonth, $selectedYear, $resolvedEmployeeIds) {
            $processor = new AttendanceService($employee);
            $data = [];

            // For half-month employees
            if ($employee->salary_duration === 'half_month') {
                // First half (1st to 15th)
                $firstStart = date('Y-m-d', strtotime("$selectedYear-$selectedMonth-01"));
                $firstEnd = date('Y-m-15', strtotime("$selectedYear-$selectedMonth-01"));
                $firstRecord = $processor->processAttendance($firstStart, $firstEnd);
                
                if ($firstRecord['missScanCount'] > 0 && !in_array("{$employee->id}:first_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $firstRecord['missScanCount'],
                        'duration' => 'first_half',
                    ];
                }

                // Second half (16th to month end)
                $secondStart = date('Y-m-16', strtotime("$selectedYear-$selectedMonth-01"));
                $secondEnd = date('Y-m-t', strtotime("$selectedYear-$selectedMonth-01"));
                $secondRecord = $processor->processAttendance($secondStart, $secondEnd);
                
                if ($secondRecord['missScanCount'] > 0 && !in_array("{$employee->id}:second_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $secondRecord['missScanCount'],
                        'duration' => 'second_half',
                    ];
                }
            } 
            // For full-month employees
            else {
                $startDate = date('Y-m-01', strtotime("$selectedYear-$selectedMonth-01"));
                $endDate = date('Y-m-t', strtotime("$selectedYear-$selectedMonth-01"));
                $record = $processor->processAttendance($startDate, $endDate);

                if ($record['missScanCount'] > 0 && !in_array($employee->id, $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $record['missScanCount'],
                        'duration' => null, // No duration for full-month
                    ];
                }
            }

            return $data;
        })->flatten(1)->filter();

        return view('pages.miss-scan.index', compact('missScanData', 'selectedMonth', 'selectedYear'));
    }

    public function resolve(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'string',
            'month' => 'required|string',
            'year' => 'required|string',
        ]);

        foreach ($request->employee_ids as $composite) {
            // Split composite value into employee ID and duration
            $parts = explode(':', $composite);
            $employeeId = $parts[0];
            $duration = count($parts) > 1 ? $parts[1] : null;

            // Validate employee exists
            if (!Employee::where('id', $employeeId)->exists()) {
                continue;
            }

            // Mark as resolved
            MissScan::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $request->month,
                    'year' => $request->year,
                    'duration' => $duration,
                ],
                []
            );
        }

        return redirect()->route('miss-scan.index', [
            'month' => $request->month,
            'year' => $request->year
        ])->with('success', 'Miss scans resolved successfully!');
    }
}
?>

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
    
    $selectedMonth = (int) $request->input('month', now()->format('m'));
    $selectedYear = $request->input('year', now()->format('Y'));
    $selectedDuration = $request->input('duration', 'full_month');
    
    // Fetch resolved entries with duration
    $resolvedEntries = MissScan::where('month', $selectedMonth)
                                ->where('year', $selectedYear)
                                ->get(['employee_id', 'duration']);

    // Map resolved entries to composite keys (e.g., "employee_id:first_half")
    $resolvedEmployeeIds = $resolvedEntries->map(function ($entry) {
        return $entry->duration 
            ? $entry->employee_id . ':' . $entry->duration 
            : $entry->employee_id . ':full_month';
    })->toArray();
    
    // Process miss-scan data
    $missScanData = $employees->map(function ($employee) use ($selectedMonth, $selectedYear, $resolvedEmployeeIds, $selectedDuration) {
        $processor = new AttendanceService($employee);
        $data = [];

        // Define date ranges based on the selected duration
        $startDate = date('Y-m-01', strtotime("$selectedYear-$selectedMonth-01"));
        $endDate = date('Y-m-t', strtotime("$selectedYear-$selectedMonth-01"));
        $midMonth = date('Y-m-15', strtotime("$selectedYear-$selectedMonth-01"));
        $sixteenthDay = date('Y-m-16', strtotime("$selectedYear-$selectedMonth-01"));

        // For half-month employees
        if ($employee->salary_duration === 'half_month') {
            // Only process records that match the selected duration or if full_month is selected
            if ($selectedDuration === 'first_half' || $selectedDuration === 'full_month') {
                // First half (1st to 15th)
                $firstRecord = $processor->processAttendance($startDate, $midMonth);
                
                if ($firstRecord && $firstRecord['missScanCount'] > 0 && !in_array("{$employee->id}:first_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $firstRecord['missScanCount'],
                        'duration' => 'first_half',
                    ];
                }
            }

            if ($selectedDuration === 'second_half' || $selectedDuration === 'full_month') {
                // Second half (16th to month end)
                $secondRecord = $processor->processAttendance($sixteenthDay, $endDate);
                
                if ($secondRecord && $secondRecord['missScanCount'] > 0 && !in_array("{$employee->id}:second_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $secondRecord['missScanCount'],
                        'duration' => 'second_half',
                    ];
                }
            }
        } 
        // For full-month employees
        else {
            // Only show full month employees when relevant
            if ($selectedDuration === 'full_month') {
                $record = $processor->processAttendance($startDate, $endDate);
                
                if ($record && $record['missScanCount'] > 0 && !in_array("{$employee->id}:full_month", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $record['missScanCount'],
                        'duration' => 'full_month', // Changed from null to 'full_month' for consistency
                    ];
                }
            } elseif ($selectedDuration === 'first_half') {
                // Process first half for full-month employees
                $record = $processor->processAttendance($startDate, $midMonth);
                
                if ($record && $record['missScanCount'] > 0 && !in_array("{$employee->id}:first_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $record['missScanCount'],
                        'duration' => 'first_half',
                    ];
                }
            } elseif ($selectedDuration === 'second_half') {
                // Process second half for full-month employees
                $record = $processor->processAttendance($sixteenthDay, $endDate);
                
                if ($record && $record['missScanCount'] > 0 && !in_array("{$employee->id}:second_half", $resolvedEmployeeIds)) {
                    $data[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $record['missScanCount'],
                        'duration' => 'second_half',
                    ];
                }
            }
        }

        return $data;
    })->flatten(1)->filter();

    return view('pages.miss-scan.index', compact('missScanData', 'selectedMonth', 'selectedYear', 'selectedDuration'));
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
            // dd($duration);
            
            if (!Employee::where('id', $employeeId)->exists()) {
                continue;
            }

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

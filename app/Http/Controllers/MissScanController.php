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
        
        $startDate = date('Y-m-d', strtotime("{$selectedYear}-{$selectedMonth}-01"));
        $endDate = date('Y-m-t', strtotime("{$selectedYear}-{$selectedMonth}-01"));
        // dd($startDate,$endDate);

        $resolvedEmployeeIds = MissScan::where('month', $selectedMonth)
                                        ->where('year', $selectedYear)
                                        ->pluck('employee_id')
                                        ->toArray();

        $missScanData = $employees->map(function ($employee) use($startDate,$endDate,$resolvedEmployeeIds) {
            $processor = new AttendanceService($employee);
            $record = $processor->processAttendance($startDate,$endDate);
            $missScanCount = $record['missScanCount'];

            if (in_array($employee->id, $resolvedEmployeeIds)) {
                return false;
            } else {
                if ($missScanCount > 3) {
                    return [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'miss_scan_count' => $missScanCount,
                    ];
                }
            }
            
            return null;
        })->filter();

        return view('pages.miss-scan.index', compact('missScanData','selectedMonth','selectedYear'));
    }

    public function resolve(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|string',
        ]);
        
        foreach ($request->employee_ids as $employeeId) {
            Missscan::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $request->month,
                    'year' => $request->year,
                ],
                []
            );
        }

        return redirect()->route('miss-scan.index', ['month' => $request->month, 'year' => $request->year])
                         ->with('success', 'Miss-scan entries resolved successfully.');
    }
}
?>

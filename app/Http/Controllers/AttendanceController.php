<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Imports\AttendanceImport;
use App\Imports\UsersInfoImport;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\UserInfo;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;

class AttendanceController extends Controller
{
    private function extractInsertStatements(string $sql): array
    {
        preg_match_all('/INSERT INTO `attendances` .*?VALUES(.*?);/si', $sql, $matches);

        return $matches[0] ?? [];
    }

    private function parseSqlData(string $sql): array
    {
        $data = [];

        // Match the `VALUES` section of the INSERT statement
        preg_match_all('/\(([^)]+)\)/', $sql, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $row) {
                // Split row into individual values
                $values = str_getcsv($row, ',', "'");

                $data[] = [
                    'id' => (int) $values[0],
                    'code' => trim($values[1], "'"),
                    'datetime' => $values[2],
                    'created_at' => $values[3],
                    'updated_at' => $values[4],
                ];
            }
        }

        return $data;
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|file'
        ]);

        try {
            set_time_limit(300); // Increase to 300 seconds (5 minutes) or any required limit
            Excel::import(new UsersInfoImport, $request->file('excel_file'));
            
            return back()->with('success', 'Users imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }

        return
        $file = $request->file('excel_file');
        $filePath = $file->getRealPath();

        try {
            $sqlContents = File::get($filePath);
        
            // Split SQL file into individual statements
            $sqlStatements = array_filter(explode(';', $sqlContents));
        
            // Filter for attendance-related SQL statements
            $attendanceStatements = array_filter($sqlStatements, function($statement) {
                return stripos($statement, 'INSERT INTO `attendances` (`id`, `code`, `datetime`, `created_at`, `updated_at`) VALUES') !== false;
            });
            $insertStatements = $this->extractInsertStatements($sqlContents);
        
            // Filter to include only today's records
            $today = now()->format('Y-m-d');

        $today = Carbon::now()->format('Y-m-d');
        
        foreach ($insertStatements as $statement) {
            $rows = $this->parseSqlData($statement);
            $filteredRows = array_filter($rows, function ($row) use ($today) {
                return strpos($row['datetime'], $today) === 0; 
            });
            $filteredRows = array_map(function($row) {
                unset($row['id']);
                return $row;
            }, $filteredRows);
            try {
                DB::table('attendances')->insert($filteredRows);
            } catch (\Exception $e) {
                return back()->with('error', 'There was an error');
            }
        }
        
            return back()->with('success', 'Success');
        } catch (\Exception $e) {
            return back()->with('error', 'Error' . $e->getMessage());
        }
        

        // $request->validate([
        //     'excel_file' => 'required'
        // ]);

        // $file = $request->file('excel_file');

        // Excel::import(new AttendanceImport, $file);

        // return redirect()->back()->with('success', 'Attendance data imported successfully');
    }

    public function calculateHours($employeeId)
    {
        // $startDate = Carbon::now()->startOfMonth();
        // $endDate = Carbon::now()->endOfMonth();

        $attendances = Attendance::where('code', $employeeId)
            // ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();

        $totalWorkingHours = 0;
        $dailyHours = [];

        for ($i = 0; $i < count($attendances) - 1; $i += 2) {
            $checkIn = Carbon::parse($attendances[$i]->datetime);
            $checkOut = Carbon::parse($attendances[$i + 1]->datetime);

            $hoursWorked = $checkIn->diffInHours($checkOut);
            $totalWorkingHours += $hoursWorked;

            $date = $checkIn->format('Y-m-d');
            if (!isset($dailyHours[$date])) {
                $dailyHours[$date] = 0;
            }
            $dailyHours[$date] += $hoursWorked;
        }

        $workingDays = 26;
        $requiredHoursPerDay = 12;
        $requiredHours = $workingDays * $requiredHoursPerDay;

        $overtime = max(0, $totalWorkingHours - $requiredHours);
        $undertime = max(0, $requiredHours - $totalWorkingHours);

        return view('pages.attendance.hours', compact('totalWorkingHours', 'requiredHours', 'overtime', 'undertime', 'dailyHours'));
    }


    public function viewAttendance(Request $request){

        $startDay = $request->input('start_date');
        $endDay = $request->input('end_date');
        // $endDay = Carbon::create($request->year, $request->month, 1)->endOfMonth()->endOfDay();
        $selection = $request->selection;
        
        
        if($selection == "department"){
            $employees = Employee::where('department_id', $request->department_id)->get();
            $allAttendances = [];
            
            foreach($employees as $employee){
                $processor = new AttendanceService($employee);
                $record = $processor->processAttendance($startDay,$endDay);
                $allAttendances [$employee->id] = $record;
            }
            
            return view('pages.attendance.show', [
                'collectiveAttendances' => $allAttendances,
            ]);
        } else {
            $employee = Employee::findOrFail($request->employee_id);
            $allAttendances = [];
    
            $processor = new AttendanceService($employee);
            $record = $processor->processAttendance($startDay,$endDay);
            $allAttendances [$employee->id] = $record;
            
            return view('pages.attendance.show', [
                'collectiveAttendances' => $allAttendances,
            ]);
        }

        
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|before_or_equal:today'
        ]);

        $employee_id = $request->employee_id;
        $date = Carbon::parse($request->date);
        
        $employee = Employee::find($employee_id);
        $userInfo = UserInfo::where('code', $employee->code)->first();

        if(!$userInfo) {
            return response()->json([
                'success' => false,
                'message' => "Employee Info not found",
            ]);
        }

        $lastRecord = Attendance::where('code', $userInfo->id)
            ->whereDate('datetime', $date)
            ->latest()
            ->first();

        // No record found - allow check-in
        if (!$lastRecord) {
            return response()->json([
                'status' => 'checkin'
            ]);
        }

        $recordsCount = Attendance::where('code', $userInfo->id)
            ->whereDate('datetime', $date)
            ->count();

        if ($recordsCount >= 2) {
            // Both check-in and check-out exist
            return response()->json([
                'status' => 'completed'
            ]);
        } else {
            // Only one record exists (check-in) - allow check-out
            return response()->json([
                'status' => 'checkout'
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|before_or_equal:today',
            'attendance_type' => 'required|in:checkin,checkout'
        ]);

        $employee_id = $request->employee_id;
        $date = Carbon::parse($request->date);
        
        $employee = Employee::find($employee_id);
        $userInfo = UserInfo::where('code', $employee->code)->first();

        if(!$userInfo) {
            return response()->json([
                'success' => false,
                'message' => "Employee Info not found",
            ]);
        }

        $lastRecord = Attendance::where('code', $userInfo->id)
            ->whereDate('datetime', $date)
            ->latest()
            ->first();

        if ($request->attendance_type === 'checkin' && $lastRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in for selected date'
            ], 422);
        }

        if ($request->attendance_type === 'checkout' && !$lastRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out without checking in first'
            ], 422);
        }
        
        $attendance = new Attendance([
            'code' => $userInfo->id,
            'datetime' => $date->setTimeFromTimeString(now()->toTimeString())
        ]);
        $attendance->save();

        $timeFormatted = Carbon::parse($attendance->datetime)->format('Y-m-d H:i:s');
        $type = $request->attendance_type === 'checkin' ? 'Check-in' : 'Check-out';

        return response()->json([
            'success' => true,
            'message' => "$type recorded successfully",
            'data' => [
                'employee_name' => $employee->name,
                'datetime' => $timeFormatted,
                'type' => $type
            ]
        ]);
    }

    public function showCorrectionForm()
    {
        $employees = Employee::get(['id','name']);
        return view('pages.attendance.correction', compact('employees'));
    }
    
    public function getAttendanceEntries(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'type' => 'required|in:checkin,checkout'
        ]);

        $date = Carbon::parse($request->date);
        
        $employee = Employee::findOrFail($request->employee_id);
        
        // Get all entries for the selected date
        $entries = Attendance::where('code', $employee->userInfo->id)
            ->whereDate('datetime', $date)
            ->orderBy('datetime', 'asc')
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No attendance records found for this date'
            ]);
        }

        // If only one entry exists, treat it as check-in
        if ($entries->count() === 1) {
            if ($request->type === 'checkin') {
                return response()->json([
                    'status' => 'success',
                    'entry' => $entries->first(),
                    'current_time' => Carbon::parse($entries->first()->datetime)->format('H:i')
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Checkout entry not found'
                ]);
            }
        }

        // For multiple entries
        $entry = $request->type === 'checkin' 
            ? $entries->first()  // First entry for check-in
            : $entries->last();  // Last entry for check-out

        return response()->json([
            'status' => 'success',
            'entry' => $entry,
            'current_time' => Carbon::parse($entry->datetime)->format('H:i')
        ]);
    }

    public function updateAttendance(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'new_time' => 'required',
            'date' => 'required|date'
        ]);

        try {
            $attendance = Attendance::findOrFail($request->attendance_id);
            
            // Combine the date with new time
            $currentDateTime = Carbon::parse($attendance->datetime);
            $newTime = Carbon::parse($request->new_time);
            
            $newDateTime = Carbon::parse($request->date)->setTime(
                $newTime->hour,
                $newTime->minute,
                $newTime->second
            );

            $attendance->datetime = $newDateTime;
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance time updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update attendance time'
            ]);
        }
    }

    public function updateAttendanceTable()
    {
        $lastAttendance = DB::table('attendances')->latest('datetime')->first();

        if (!$lastAttendance) {
            return response()->json(['message' => 'No records found in attendances table'], 404);
        }

        $lastDate = \Carbon\Carbon::parse($lastAttendance->datetime)->addDay()->toDateString();

        $startDate = $lastDate;
        $endDate = Carbon::yesterday()->toDateString();

        $recordsInRange = DB::connection('mysql2')->table('CHECKINOUT')
            ->select(
                'USERID',
                DB::raw('TRIM(BOTH \'"\' FROM CHECKTIME) as clean_time')
            )
            ->get()
            ->filter(function ($record) use ($startDate, $endDate) {

                $checktime = Carbon::createFromFormat('m/d/y H:i:s', $record->clean_time)->format('Y-m-d');

                // Check if the date falls within the range
                return $checktime >= $startDate && $checktime <= $endDate;
            });

        if ($recordsInRange->isEmpty()) {
            return response()->json(['message' => 'No records found in the given date range','startDate' => $startDate, 'endDate' => $endDate], 404);
        }
        // Process records in chunks
        $chunkSize = 200;
        $recordsInRange->chunk($chunkSize)->each(function ($chunk) {
            $attendanceData = [];

            foreach ($chunk as $record) {
                try {
                    $datetime = Carbon::createFromFormat('m/d/y H:i:s', $record->clean_time)->format('Y-m-d H:i:s');
                    $attendanceData[] = [
                        'code' => $record->USERID,
                        'datetime' => $datetime,
                    ];
                } catch (\Exception $e) {
                    dd($e);
                }
            }

            if (!empty($attendanceData)) {
                DB::table('attendances')->insert($attendanceData);
            }
        });

        return response()->json(['message' => 'Attendance records created successfully', 'startDate' => $startDate, 'endDate' => $endDate]);
    }
    
    
}
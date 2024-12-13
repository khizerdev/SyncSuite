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

    private function createAttd($employee,$startDay,$endDay,$request){
        $userInfo = UserInfo::where('code' , $employee->code)->first();

        if(!$userInfo){
            return;
        }

        $attendances = Attendance::where('code', $userInfo->id)
        ->whereBetween('datetime', [$startDay, $endDay])
        ->orderBy('datetime')
        ->get();

        $shift = $employee->timings;
        
        $holidays = explode(',', $employee->type->holidays);
        $holidays = array_map('trim', $holidays);
        $holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 1;
        $overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 1;

        $dailyMinutes = [];
        $groupedAttendances = [];
    
        $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));
        $totalOvertimeMinutes = 0;

        $startDate = Carbon::create($request->year, $request->month, 1);
        $endDate = Carbon::create($request->year, $request->month, Carbon::parse($endDay)->day);
        $workingDays = 0;

        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');
            $groupedAttendances[$date] = [];
            $dailyMinutes[$date] = 0;
            // check if it is holiday for an employee
            if (!in_array($startDate->format('l'), $holidays)) {
                $workingDays++;
            }
            $startDate->addDay();
        }

        $hoursPerDay = 12;
        $totalExpectedWorkingHours = $workingDays * $hoursPerDay;

        for ($i = 0; $i < count($attendances); $i++) {
            $checkIn = Carbon::parse($attendances[$i]->datetime);
            $date = $checkIn->format('Y-m-d');
            if($groupedAttendances[$date]){
                continue;
            }
        
            // Collect all entries for the current date
            $currentDateEntries = [];
            $currentIndex = $i;
            
            while ($currentIndex < count($attendances)) {
                $entry = Carbon::parse($attendances[$currentIndex]->datetime);
                if ($entry->format('Y-m-d') == $date) {
                    $currentDateEntries[] = $attendances[$currentIndex];
                    $currentIndex++;
                } else {
                    break;
                }
            }
        
            // If we have entries for current date
            if (count($currentDateEntries) > 0) {
                $entriesCount = count($currentDateEntries);
                
                // Determine check-in and check-out based on count
                if ($entriesCount % 2 == 0) {
                    // Even number of entries - take last pair
                    $checkIn = Carbon::parse($currentDateEntries[0]->datetime);
                    $checkOut = Carbon::parse($currentDateEntries[$entriesCount - 1]->datetime);
                } else {
                    // Odd number of entries - take second last entry as checkout
                    $checkIn = Carbon::parse($currentDateEntries[0]->datetime);
                    $checkOut = Carbon::parse($currentDateEntries[$entriesCount - 2]->datetime);
                }
        
                $shiftStart = Carbon::parse($shift->start_time);
                $shiftEnd = Carbon::parse($shift->end_time);
                
                if ($isNightShift) {
                    $shiftEnd->addDay();
                }
                $maxCheckOut = $shiftEnd->copy()->addHours(4);
        
                // Validate checkout time against max allowed
                if ($checkOut > $maxCheckOut) {
                    $checkOut = null;
                }
        
                if ($isNightShift) {
                    $calculationCheckIn = $checkIn->copy()->addHours(6);
                    $calculationCheckOut = $checkOut ? $checkOut->copy()->addHours(6) : null;
                } else {
                    $calculationCheckIn = $checkIn;
                    $calculationCheckOut = $checkOut;
                }
        
                $groupedAttendances[$date][] = [
                    'original_checkin' => $checkIn,
                    'original_checkout' => $checkOut,
                    'calculation_checkin' => $calculationCheckIn,
                    'calculation_checkout' => $calculationCheckOut,
                    'is_incomplete' => !$checkOut
                ];
        
                // Update outer loop counter
                $i = $currentIndex - 1;
            }
        }
    
        $totalMinutesWorked = 0;
        $totalHolidayMinutesWorked = 0;
    
        foreach ($groupedAttendances as $date => $entries) {
            $shiftStartTime = Carbon::parse($shift->start_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');
            $shiftEndTime = Carbon::parse($shift->end_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');
    
            $shiftStart = Carbon::parse($date . ' ' . $shiftStartTime);
            $shiftEnd = Carbon::parse($date . ' ' . $shiftEndTime);
    
            if ($isNightShift) {
                $shiftEnd->addDay();
            }
    
            $totalMinutes = 0;
            $overtimeMinutes = 0;
    
            foreach ($entries as $entry) {
                if (!$entry['is_incomplete']) {
                    $entryTimeStart = $entry['calculation_checkin'];
                    $entryTimeEnd = $entry['calculation_checkout'];
    
                    $startTime = $entryTimeStart->max($shiftStart);
                    $endTime = $entryTimeEnd->min($shiftEnd);
    
                    if ($startTime->lt($endTime)) {
                        $minutesWorked = $startTime->diffInMinutes($endTime);
                        $totalMinutes += $minutesWorked;
    
                        // checking if it is holiday
                        $dayOfWeek = Carbon::parse($date)->format('l');
                        if (in_array($dayOfWeek, $holidays)) {
                            $totalHolidayMinutesWorked += $minutesWorked;
                        }
    
                        $workedMinutes = $entryTimeStart->diffInMinutes($entryTimeEnd);
                        // if exceed 12 hours calculate overtime
                        if ($workedMinutes > 720) {
                            $overtimeMinutes += $workedMinutes - 720; // extra minutes
                        }
                    }
                }
            }
    
            $dailyMinutes[$date] = $totalMinutes;
            $totalMinutesWorked += $totalMinutes;
            $totalOvertimeMinutes += $overtimeMinutes;
        }

        $totalHoursWorked = $totalMinutesWorked / 60;
        $totalHolidayHoursWorked = $totalHolidayMinutesWorked / 60;

        return [
            'employee' => $employee,
            'dailyMinutes' => $dailyMinutes,
            'totalHoursWorked' => $totalHoursWorked,
            'workingDays' => $workingDays,
            'totalHolidayHoursWorked' => $totalHolidayHoursWorked,
            'holidays' => $holidays,
            'totalOvertimeMinutes' => $totalOvertimeMinutes,
            'isNightShift' => $isNightShift,
            'shift' => $shift,
            'groupedAttendances' => $groupedAttendances,
        ];
    }

    public function viewAttendance(Request $request){

        $startDay = $request->input('start_date');
        $endDay = Carbon::create($request->year, $request->month, 1)->endOfMonth()->endOfDay();
        $selection = $request->selection;
        
        
        if($selection == "department"){
            $employees = Employee::where('department_id', $request->department_id)->get();
            $allAttendances = [];
            
            foreach($employees as $employee){
                $record = $this->createAttd($employee,$startDay,$endDay,$request);
                $allAttendances [$employee->id] = $record;
            }
            
            return view('pages.attendance.show', [
                'collectiveAttendances' => $allAttendances,
            ]);
        } else {
            $employee = Employee::findOrFail($request->employee_id);
            $allAttendances = [];
    
                $record = $this->createAttd($employee,$startDay,$endDay,$request);
                $allAttendances [$employee->id] = $record;
    
            return view('pages.attendance.show', [
                'collectiveAttendances' => $allAttendances,
            ]);
        }

        
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $today = Carbon::today();

        $employee_id = $request->employee_id;
        $employee = Employee::find($employee_id);
        $userInfo = UserInfo::where('code' , $employee->code)->first();

        if(!$userInfo){
            return response()->json([
                'success' => false,
                'message' => "Employee Info not found",
            ]);
        }

        $lastRecord = Attendance::where('code', $userInfo->id)
            ->whereDate('datetime', Carbon::today())
            ->latest()
            ->first();

        if ($request->attendance_type === 'checkin' && $lastRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today'
            ], 422);
        }
    
        if ($request->attendance_type === 'checkout' && !$lastRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out at this time'
            ], 422);
        }
        
        $attendance = new Attendance([
            'code' => $userInfo->id,
            'datetime' => now()
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

    public function checkStatus(Request $request)
    {
        $employee_id = $request->employee_id;
        $employee = Employee::find($employee_id);
        $userInfo = UserInfo::where('code' , $employee->code)->first();

        if(!$userInfo){
            return response()->json([
                'success' => false,
                'message' => "Employee Info not found",
            ]);
        }

        $lastRecord = Attendance::where('code', $userInfo->id)
            ->whereDate('datetime', Carbon::today())
            ->latest()
            ->first();

        // No record found - allow check-in
        if (!$lastRecord) {
            return response()->json([
                'status' => 'checkin'
            ]);
        }

        // Get count of today's records for this employee
        $todayRecordsCount = Attendance::where('code', $userInfo->id)
        ->whereDate('datetime', Carbon::today())
        ->count();

        if ($todayRecordsCount >= 2) {
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


    
    
}
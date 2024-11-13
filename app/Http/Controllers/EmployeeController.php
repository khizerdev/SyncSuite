<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\AdvanceSalary;
use App\Models\Employee;
use App\Models\Attachment;
use App\Models\Attendance;
use App\Models\Loan;
use App\Models\Salary;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-employee|create-employee|edit-employee|delete-employee' => ['only' => ['index', 'store']],
             'permission:create-employee' => ['only' => ['create', 'store']],
             'permission:edit-employee' => ['only' => ['edit', 'update']],
             'permission:delete-employee' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
            $query = Employee::with('department');
        
            if ($request->has('department_id') && !empty($request->department_id)) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->has('type_id') && !empty($request->type_id)) {
                $query->where('type_id', $request->type_id);
            }
    
            $data = $query->latest()->get();

             return DataTables::of($data)
                ->addColumn('department_name', function ($data) {
                    return $data->department_id ? $data->department->name : 'N/A';
                })
                ->addColumn('type_name', function ($data) {
                    return $data->type->id ? $data->type->name : 'N/A';
                })
                ->addColumn('action', function($row){
                    $editUrl = route('employees.edit', $row->id);
                    $attdUrl = route('employees.attd', $row->id);
                    $deleteUrl = route('employees.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>';
                    $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/employees/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2"><i class="fas fa-trash"></i></button>';
                    
                    // $btn .= '<a href="'.$attdUrl.'" class="btn btn-warning btn-sm">Payroll Information</a>';
                    $btn .= '<button data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-warning btn-show-employee" data-employee-id="' . $row->id . '" data-employee-name="' . $row->name . '">Payroll Information</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.employees.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $employee = Employee::create($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }

            
            
            // Handle Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Employee created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = Employee::with('attachments')->findOrFail($id);
        return view('pages.employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $validatedData = $request->validated();

            $employee->update($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                // Delete the old profile picture if exists
                $oldProfilePicture = $employee->attachments()->where('file_type', 'like', 'image%')->first();
                if ($oldProfilePicture) {
                    Storage::delete($oldProfilePicture->file_path);
                    $oldProfilePicture->delete();
                }

                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                // Delete the old resume if exists
                $oldResume = $employee->attachments()->where('file_type', 'application/pdf')->first();
                if ($oldResume) {
                    Storage::delete($oldResume->file_path);
                    $oldResume->delete();
                }

                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }


            // Handle Documents
            if ($request->hasFile('documents')) {
                // Delete the old documents if exists
                $oldDocuments = $employee->attachments()->where('file_type', '!=', 'application/pdf')->where('file_type', '!=', 'image%')->get();
                foreach ($oldDocuments as $oldDocument) {
                    Storage::delete($oldDocument->file_path);
                    $oldDocument->delete();
                }

                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }


            return redirect()->route('employees.index')->with('success', 'Updated successfully.');
        } catch (ValidationException $e) {
            dd($e);
            return redirect()->route('employees.index')->with('error', 'Validation failed');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('employees.index')->with('error', 'ailed to update employee');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
    
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete employee', 'error' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);
        $pathToFile = storage_path('app/' . $attachment->file_path);

        return response()->download($pathToFile, $attachment->file_name);
    }

   
    public function attd(Request $request, $employeeId)
    {
        $months = array(
            "January", "February", "March", "April", "May",
            "June", "July", "August", "September", "October",
            "November", "December"
        );
        
        $month = $months[$request->month - 1];

        // dd($request->all());
        
        $salary = Salary::where('employee_id' , $employeeId)->where('month', $month)
        ->where('year', $request->year)->first();

        if(!$salary){
            $employee = Employee::findOrFail($employeeId);
            $shift = $employee->timings;
        
            $holidays = explode(',', $employee->type->holidays);
            $holidays = array_map('trim', $holidays);
            $holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 1;
            $overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 1;
        
        
            $dailyMinutes = [];
            $groupedAttendances = [];
        
            $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));
            $totalOvertimeMinutes = 0;
        
            // Calculate the number of working days in current month 2024
            $startDate = Carbon::create($request->year, $request->month, 1);
            $endDate = Carbon::create($request->year, $request->month, $startDate->daysInMonth);
            $attendances = Attendance::where('code', $employee->code)
            ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();

            $workingDays = 0;
        
            while ($startDate->lte($endDate)) {
                $date = $startDate->format('Y-m-d');
                $groupedAttendances[$date] = []; // Empty by default
                $dailyMinutes[$date] = 0; // Default to 0 minutes
                // Check if the day is not a holiday for the employee
                if (!in_array($startDate->format('l'), $holidays)) {
                    $workingDays++;
                }
                $startDate->addDay();
            }
        
            // Total number of hours the employee is expected to work in July
            $hoursPerDay = 12;
            $totalExpectedWorkingHours = $workingDays * $hoursPerDay;
        
            // Calculate salary per hour
            $salaryPerMonth = $employee->salary;
            $salaryPerHour = $salaryPerMonth / $totalExpectedWorkingHours;
        
            for ($i = 0; $i < count($attendances); $i++) {
                $checkIn = Carbon::parse($attendances[$i]->datetime);
                $date = $checkIn->format('Y-m-d');
        
                // Find the next check-out or check-in
                $nextEntry = null;
                for ($j = $i + 1; $j < count($attendances); $j++) {
                    $nextEntry = Carbon::parse($attendances[$j]->datetime);  
                    if (abs($nextEntry->diffInHours($checkIn)) <= 16) {
                        break;
                    }
                    $nextEntry = null;
                }
                $shiftStart = Carbon::parse($shift->start_time);
                $shiftEnd = Carbon::parse($shift->end_time);
                if ($isNightShift) {
                    $shiftEnd->addDay();
                }
                $maxCheckOut = $shiftEnd->copy()->addHours(4);
        
                if ($nextEntry && $nextEntry <= $maxCheckOut) {
                    $checkOut = $nextEntry;
                    $i = $j;
                } else {
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
        
                            // Check if the date is a holiday
                            $dayOfWeek = Carbon::parse($date)->format('l');
                            if (in_array($dayOfWeek, $holidays)) {
                                $totalHolidayMinutesWorked += $minutesWorked;
                            }
        
                            $workedMinutes = $entryTimeStart->diffInMinutes($entryTimeEnd);
                            // Calculate overtime if worked minutes exceed standard 12 hours
                            if ($workedMinutes > 720) { // 12 hours * 60 minutes
                                $overtimeMinutes += $workedMinutes - 720; // Overtime is the extra minutes
                            }
                        }
                    }
                }
        
                $dailyMinutes[$date] = $totalMinutes;
                $totalMinutesWorked += $totalMinutes;
                $totalOvertimeMinutes += $overtimeMinutes;
            }
        
            // Convert total minutes worked to hours
            $totalHoursWorked = $totalMinutesWorked / 60;
            $totalHolidayHoursWorked = $totalHolidayMinutesWorked / 60;
        
            $regularHoursWorked = $totalHoursWorked;
            $overtimeAmount = number_format((number_format($totalOvertimeMinutes, 2) / 60)*($overTimeRatio*$salaryPerHour) , 2);
            $actualSalaryEarned = ($regularHoursWorked * $salaryPerHour) + ($totalHolidayHoursWorked * $salaryPerHour * $holidayRatio) + $overtimeAmount;

            $totalExpectedWorkingDays = number_format($workingDays * 12, 2);
            $totalOverTimeHoursWorked = number_format($totalOvertimeMinutes, 2) / 60;
            $totalOvertimePay = number_format((number_format($totalOvertimeMinutes, 2) / 60)*($overTimeRatio*$salaryPerHour) , 2);

            $advance = AdvanceSalary::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->first();

            $activeLoan = Loan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

            $loanInstallmentAmount = 0;


            // Calculate loan installment if there's an active loan
            if($activeLoan && $request->include_loan == "1") {
                $loanInstallmentAmount = $activeLoan->amount / $activeLoan->months;
                $activeLoan->balance = $activeLoan->balance+$loanInstallmentAmount;
                if($activeLoan->amount == ($activeLoan->balance+$loanInstallmentAmount)){
                    $activeLoan->status == 'paid';
                }
                $activeLoan->save();
            }

            $salary = null;
            if($advance){
                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $request->year,
                    'current_salary' => $employee->salary,
                    'expected_hours' => $totalExpectedWorkingDays,
                    'normal_hours' => $totalHoursWorked,
                    'holiday_hours' => $totalHolidayHoursWorked,
                    'overtime_hours' => $totalOverTimeHoursWorked,
                    'salary_per_hour' => $salaryPerHour,
                    'holiday_pay_ratio' => $holidayRatio,
                    'overtime_pay_ratio' => $totalOverTimeHoursWorked,
                    'overtime_hours' => $overTimeRatio,
                    'holidays' => $employee->type->holidays,
                    'advance_deducted' => $advance->amount,
                    'loan_deducted' => $loanInstallmentAmount
                ]);
                $advance->is_paid = 1;
                $advance->save();
            } else {
                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $request->year,
                    'current_salary' => $employee->salary,
                    'expected_hours' => $totalExpectedWorkingDays,
                    'normal_hours' => $totalHoursWorked,
                    'holiday_hours' => $totalHolidayHoursWorked,
                    'overtime_hours' => $totalOverTimeHoursWorked,
                    'salary_per_hour' => $salaryPerHour,
                    'holiday_pay_ratio' => $holidayRatio,
                    'overtime_pay_ratio' => $totalOverTimeHoursWorked,
                    'overtime_hours' => $overTimeRatio,
                    'holidays' => $employee->type->holidays,
                    'advance_deducted' => 0,
                    'loan_deducted' => $loanInstallmentAmount
                ]);
            }

            return view('pages.employees.attendance', compact('groupedAttendances', 'dailyMinutes', 'employee', 'shift', 'isNightShift', 'actualSalaryEarned', 'totalHoursWorked', 'salaryPerHour', 'workingDays', 'totalHolidayHoursWorked', 'holidayRatio','holidays','totalOvertimeMinutes','overTimeRatio','totalExpectedWorkingDays','totalOverTimeHoursWorked','totalOvertimePay','salary'));
        } else {
            $employee = Employee::findOrFail($employeeId);
            $shift = $employee->timings;
            
            $holidays = explode(',', $salary->holidays);
            $holidays = array_map('trim', $holidays);
            $holidayRatio = $salary->holiday_pay_ratio;
            $overTimeRatio = $salary->overtime_pay_ratio;
        
            // $attendances = Attendance::where('code', $employee->code)
            //     ->orderBy('datetime')
            //     ->get();
        
            $dailyMinutes = [];
            $groupedAttendances = [];
        
            $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));
            $totalOvertimeMinutes = 0;
        
            // Calculate the number of working days in July 2024
            $startDate = Carbon::create($request->year, $request->month, 1);
            $endDate = Carbon::create($request->year, $request->month, $startDate->daysInMonth);
            $attendances = Attendance::where('code', $employee->code)
            ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();
            $workingDays = 0;
        
            while ($startDate->lte($endDate)) {
                $date = $startDate->format('Y-m-d');
                $groupedAttendances[$date] = []; // Empty by default
                $dailyMinutes[$date] = 0; // Default to 0 minutes
                // Check if the day is not a holiday for the employee
                if (!in_array($startDate->format('l'), $holidays)) {
                    $workingDays++;
                }
                $startDate->addDay();
            }
        
            // Total number of hours the employee is expected to work in July
            $hoursPerDay = 12;
            $totalExpectedWorkingHours = $workingDays * $hoursPerDay;
        
            // Calculate salary per hour
            $salaryPerMonth = $salary->current_salary;
            $salaryPerHour = $salaryPerMonth / $totalExpectedWorkingHours;
        
            for ($i = 0; $i < count($attendances); $i++) {
                $checkIn = Carbon::parse($attendances[$i]->datetime);
                $date = $checkIn->format('Y-m-d');
        
                // Find the next check-out or check-in
                $nextEntry = null;
                for ($j = $i + 1; $j < count($attendances); $j++) {
                    $nextEntry = Carbon::parse($attendances[$j]->datetime);  
                    if (abs($nextEntry->diffInHours($checkIn)) <= 16) {
                        break;
                    }
                    $nextEntry = null;
                }
                $shiftStart = Carbon::parse($shift->start_time);
                $shiftEnd = Carbon::parse($shift->end_time);
                if ($isNightShift) {
                    $shiftEnd->addDay();
                }
                $maxCheckOut = $shiftEnd->copy()->addHours(4);
        
                if ($nextEntry && $nextEntry <= $maxCheckOut) {
                    $checkOut = $nextEntry;
                    $i = $j;
                } else {
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
        
                            // Check if the date is a holiday
                            $dayOfWeek = Carbon::parse($date)->format('l');
                            if (in_array($dayOfWeek, $holidays)) {
                                $totalHolidayMinutesWorked += $minutesWorked;
                            }
        
                            $workedMinutes = $entryTimeStart->diffInMinutes($entryTimeEnd);
                            // Calculate overtime if worked minutes exceed standard 12 hours
                            if ($workedMinutes > 720) { // 12 hours * 60 minutes
                                $overtimeMinutes += $workedMinutes - 720; // Overtime is the extra minutes
                            }
                        }
                    }
                }
        
                $dailyMinutes[$date] = $totalMinutes;
                $totalMinutesWorked += $totalMinutes;
                $totalOvertimeMinutes += $overtimeMinutes;
            }
        
            // Convert total minutes worked to hours
            $totalHoursWorked = $salary->normal_hours;
            $totalHolidayHoursWorked = $salary->holiday_hours;
        
            $regularHoursWorked = $totalHoursWorked;
            $overtimeAmount = number_format((number_format($totalOvertimeMinutes, 2) / 60)*($salary->overtime_pay_ratio*$salary->salary_per_hour) , 2);
            $actualSalaryEarned = ($regularHoursWorked * $salary->salary_per_hour) + ($salary->holiday_hours * $salary->salary_per_hour * $holidayRatio) + $overtimeAmount;

            $totalExpectedWorkingDays = number_format($workingDays * 12, 2);
            $totalOverTimeHoursWorked = number_format($salary->overtime_pay_ratio, 2) / 60;
            $totalOvertimePay = number_format((number_format($totalOvertimeMinutes, 2) / 60)*($overTimeRatio*$salary->salary_per_hour) , 2);
        
        
            return view('pages.employees.attendance', compact('groupedAttendances', 'dailyMinutes', 'employee', 'shift', 'isNightShift', 'actualSalaryEarned', 'totalHoursWorked', 'salaryPerHour', 'workingDays', 'totalHolidayHoursWorked', 'holidayRatio','holidays','totalOvertimeMinutes','overTimeRatio','totalExpectedWorkingDays','totalOverTimeHoursWorked','totalOvertimePay','salary'));
        }
    }

    public function calculateSalaryForAdvance($employeeId){
        $employee = Employee::findOrFail($employeeId);
        $shift = $employee->timings;
    
        $holidays = explode(',', $employee->type->holidays);
        $holidays = array_map('trim', $holidays);
        $holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 1; // Default to 1 if not set
        $overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 1;  // Default to 1 if not set
    
        $attendances = Attendance::where('code', $employee->code)
        ->whereMonth('datetime', Carbon::now()->month)
        ->whereYear('datetime', Carbon::now()->year)
        ->orderBy('datetime')
        ->get();
    
        $dailyMinutes = [];
        $groupedAttendances = [];
    
        $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));
        $totalOvertimeMinutes = 0;
    
        // Calculate the number of working days in July 2024
        $startDate = Carbon::create(2024, 8, 1);
        $endDate = Carbon::create(2024, 8, 31);
        $workingDays = 0;
    
        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');
            $groupedAttendances[$date] = []; // Empty by default
            $dailyMinutes[$date] = 0; // Default to 0 minutes
            // Check if the day is not a holiday for the employee
            if (!in_array($startDate->format('l'), $holidays)) {
                $workingDays++;
            }
            $startDate->addDay();
        }
    
        // Total number of hours the employee is expected to work in July
        $hoursPerDay = 12;
        $totalExpectedWorkingHours = $workingDays * $hoursPerDay;
    
        // Calculate salary per hour
        $salaryPerMonth = $employee->salary;
        $salaryPerHour = $salaryPerMonth / $totalExpectedWorkingHours;
    
        for ($i = 0; $i < count($attendances); $i++) {
            $checkIn = Carbon::parse($attendances[$i]->datetime);
            $date = $checkIn->format('Y-m-d');
    
            // Find the next check-out or check-in
            $nextEntry = null;
            for ($j = $i + 1; $j < count($attendances); $j++) {
                $nextEntry = Carbon::parse($attendances[$j]->datetime);  
                if (abs($nextEntry->diffInHours($checkIn)) <= 16) {
                    break;
                }
                $nextEntry = null;
            }
            $shiftStart = Carbon::parse($shift->start_time);
            $shiftEnd = Carbon::parse($shift->end_time);
            if ($isNightShift) {
                $shiftEnd->addDay();
            }
            $maxCheckOut = $shiftEnd->copy()->addHours(4);
    
            if ($nextEntry && $nextEntry <= $maxCheckOut) {
                $checkOut = $nextEntry;
                $i = $j;
            } else {
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
    
                        // Check if the date is a holiday
                        $dayOfWeek = Carbon::parse($date)->format('l');
                        if (in_array($dayOfWeek, $holidays)) {
                            $totalHolidayMinutesWorked += $minutesWorked;
                        }
    
                        $workedMinutes = $entryTimeStart->diffInMinutes($entryTimeEnd);
                        // Calculate overtime if worked minutes exceed standard 12 hours
                        if ($workedMinutes > 720) { // 12 hours * 60 minutes
                            $overtimeMinutes += $workedMinutes - 720; // Overtime is the extra minutes
                        }
                    }
                }
            }
    
            $dailyMinutes[$date] = $totalMinutes;
            $totalMinutesWorked += $totalMinutes;
            $totalOvertimeMinutes += $overtimeMinutes;
        }
    
        // Convert total minutes worked to hours
        $totalHoursWorked = $totalMinutesWorked / 60;
        $totalHolidayHoursWorked = $totalHolidayMinutesWorked / 60;
    
        $regularHoursWorked = $totalHoursWorked;
        $overtimeAmount = number_format((number_format($totalOvertimeMinutes, 2) / 60)*($overTimeRatio*$salaryPerHour) , 2);
        return ($regularHoursWorked * $salaryPerHour) + ($totalHolidayHoursWorked * $salaryPerHour * $holidayRatio) + $overtimeAmount;
    }

    
}
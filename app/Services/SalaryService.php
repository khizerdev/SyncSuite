<?php

namespace App\Services;

use App\Models\AdvanceSalary;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Salary;
use App\Models\UserInfo;
use Carbon\Carbon;
use Exception;

class SalaryService
{
    public function calculateSalary($employeeId, $startDate, $endDate, $period, $currentMonth)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            $shift = $employee->timings;
            $currentMonth = $currentMonth;

            $timestamp = mktime(0, 0, 0, $currentMonth, 1, 1970);
            $currentMonthNum = date("F", $timestamp);

            $currentYear = date('Y');
    
            // Check for conflicting salary records
            $salary = Salary::where('employee_id', $employeeId)
            ->where(function ($query) use ($period) {
                if ($period === 'full_month') {
                    $query->where('period', 'first_half')
                        ->orWhere('period', 'second_half');
                } else {
                    $query->where('period', 'full_month');
                }
            })
            ->whereYear('start_date', Carbon::parse($currentMonth)->year)
            ->whereMonth('start_date', Carbon::parse($currentMonth)->month)
            ->exists();
    
            if($salary) {
                return;
            }
            
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
            
            // $startDate = Carbon::create($currentYear, $currentMonthNum, 1);
            // $endDate = Carbon::create($currentYear, $currentMonthNum, $startDate->daysInMonth);

            $userInfo = UserInfo::where('code' , $employee->code)->firstOrFail();

            $attendances = Attendance::where('code', $userInfo->id)
            ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();
            
            $workingDays = 0;
            
            $startingDate = clone $startDate;
            while ($startingDate->lte($endDate)) {
                $date = $startingDate->format('Y-m-d');
                $groupedAttendances[$date] = [];
                $dailyMinutes[$date] = 0; // Default to 0 minutes
                // Check if the day is not a holiday for the employee
                if (!in_array($startingDate->format('l'), $holidays)) {
                    $workingDays++;
                }
                $startingDate->addDay();
            }
        
            // Total number of hours the employee is expected to work
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
    
            $loan = Loan::where('employee_id', $employeeId)->whereColumn('paid', '<', 'amount')->first();

            $loanInstallmentAmount = isset($loan) ? $loan->amount / $loan->months : 0;
    
            $loanException = $employee->loanExceptions()
            ->where('month', $currentMonthNum)
            ->where('year', $currentYear)
            ->first();
    
            if($loan && $loanException && !$loanException->is_approved){
                $loan->paid += $loan->amount / $loan->months;
                $loan->save();
            }
            
            $salary = null;
            if($advance){
                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'month' => $currentMonth,
                    'year' => $currentYear,
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
                    'period' => $period,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'loan_deducted' => $loanException && $loanException->is_approved ? 0 : $loanInstallmentAmount
                ]);
                $advance->is_paid = 1;
                $advance->save();
            } else {
                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'month' => $currentMonth,
                    'year' => $currentYear,
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
                    'period' => $period,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'loan_deducted' => $loanException && $loanException->is_approved ? 0 : $loanInstallmentAmount
                ]);
        } 
        } catch (Exception $e) {
            dd($e);
        }

    }
}
<?php

namespace App\Services;

use App\Models\AdvanceSalary;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Missscan;
use App\Models\Salary;
use App\Models\UserInfo;
use Carbon\Carbon;
use Exception;

class SalaryService
{
    private $employee;
    private $attendanceData;
    private $period;
    private $currentMonth;
    private $monthDays;
    
    public function __construct($employee, $attendanceData,$period,$currentMonth)
    {
        $this->employee = $employee;
        $this->attendanceData = $attendanceData;
        $this->period = $period;
        $this->currentMonth = $currentMonth;
        
        $this->monthDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, now()->year);
    }

    public function calculateTimeDifference($data) 
    {
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        
        // Calculate the difference
        $diffInHours = $startTime->diffInHours($endTime);
        $diffInMinutes = $startTime->diffInMinutes($endTime) % 60;
        
        // Format the difference
        $difference = [
            'hours' => $diffInHours,
            'minutes' => $diffInMinutes,
            'total_minutes' => $startTime->diffInMinutes($endTime),
            'formatted' => $diffInHours
        ];
        
        return $difference;
    }

    public function calculateSalary()
    {
        $timings = $this->calculateTimeDifference($this->employee->timings);
        $hoursPerDay = intval($timings['formatted']);
        $salaryPerHour = ($this->employee->salary / $this->monthDays) / $hoursPerDay;

        $regularPay = (int) floor($this->attendanceData['totalHoursWorked']) * $salaryPerHour;
        
        $holidayPay = $this->attendanceData['totalHolidayHoursWorked'] * $salaryPerHour * $this->employee->type->holiday_ratio;
        $sandWhichViolations = $this->countSandwichRuleViolations($this->attendanceData['groupedAttendances'], $this->attendanceData['gazatteHolidays']);

        $overtimePay = ($this->attendanceData['totalOvertimeMinutes'] / 60) * $this->employee->type->overtime_ratio * $salaryPerHour;
        
        $lateMinutes = array_sum($this->attendanceData['lateMinutes']);
        $lateCutAmount = ($lateMinutes / 60) * $salaryPerHour;
        
        $normalHolidayPay = $this->attendanceData['holidayDays'] * $salaryPerHour * $hoursPerDay;

        $gazattePay = ($this->attendanceData['gazatteMinutes'] / 60) * $this->employee->type->holiday_ratio * $salaryPerHour;

        $missScanCount = $this->attendanceData["missScanCount"];
        
        $missScanCleared = Missscan::where('employee_id' , $this->employee->id)->where('month' , $this->attendanceData["month"])->where('year' , $this->attendanceData["year"])->first();
        
        // $actualSalary = ($regularPay + $holidayPay + $overtimePay+$normalHolidayPay) - $lateCutAmount;
        $actualSalary = ($regularPay + $holidayPay + $overtimePay+$normalHolidayPay);
        
        $missDeductDays = 0;
        $missAmount = 0;
        $missScanPerDayAmount = 0;

        $missDaysAmount = 0;

        if ($missScanCleared) {
            $missScanPerDayAmount = $this->employee->salary / $this->monthDays;
            $dayRatio = (int)floor($missScanCount / 3); // 0.33 => 0

            $missAmount = ($missScanCount-$dayRatio)*$missScanPerDayAmount;
            $missDeductDays = $missScanCount;

            $actualSalary += $missAmount;
            $missDaysAmount = ($missScanCount * $missScanPerDayAmount) - $missAmount;
        }

        $perDayAmount = $this->employee->salary / $this->monthDays;
        if($sandWhichViolations > 0){
            $sanwichDeductedAmount = $perDayAmount * $sandWhichViolations;
            $actualSalary -= $sanwichDeductedAmount;
        }
  
        return [
            'actualSalaryEarned' => $actualSalary,
            
            'totalExpectedWorkingHours' => number_format($this->attendanceData['workingDays'] * $hoursPerDay, 2),

            'totalOverTimeHoursWorked' => $this->attendanceData['totalOvertimeMinutes'] / 60,
            'totalOvertimeMinutes' => $this->attendanceData['totalOvertimeMinutes'],
            'totalOvertimeMinutesArray' => $this->attendanceData['overMinutes'],
            'totalOvertimePay' => number_format($overtimePay, 2, '.', ''),

            'salaryPerHour' => $salaryPerHour,
            'holidayPay' => $holidayPay,
            'normalHolidayPay' => $normalHolidayPay,
            'gazattePay' => $gazattePay,
            'gazatteHolidays' => $this->attendanceData['gazatteHolidays'],
            
            'missDeductDays' => $missDeductDays,
            'sandwichDeduct' => $sandWhichViolations. " Days - Amount ".$perDayAmount*$sandWhichViolations,
            'missAmount' => $missDaysAmount,
            'lateCutAmount' => $lateCutAmount,
        ];
    }

    private function countSandwichRuleViolations($groupedAttendances, $gazatteHolidays) {
        $violations = 0;
        $dates = array_keys($groupedAttendances);
        $gazetteHolidayDates = $gazatteHolidays->pluck('holiday_date')->toArray();
    
        foreach ($dates as $date) {

            // skip if it's not a leave day
            if (count($groupedAttendances[$date]) < 1) {
                continue;
            }
    
            if (in_array($date, $gazetteHolidayDates)) {
                continue;
            }
    
            // previous day
            $prevDay = date('Y-m-d', strtotime($date . ' -1 day'));
            $prevDayIsWorkingOrHoliday = isset($groupedAttendances[$prevDay]) && (count($groupedAttendances[$prevDay]) < 1 || in_array($prevDay, $gazetteHolidayDates));
    
            // next day
            $nextDay = date('Y-m-d', strtotime($date . ' +1 day'));
            $nextDayIsWorkingOrHoliday = isset($groupedAttendances[$nextDay]) && (count($groupedAttendances[$nextDay]) < 1 || in_array($nextDay, $gazetteHolidayDates));
    
            // if both previous and next days are working days or holidays, it's a violation
            if ($prevDayIsWorkingOrHoliday && $nextDayIsWorkingOrHoliday) {
                $violations++;
            }
        }
    
        return $violations;
    }

    
}
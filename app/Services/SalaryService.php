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
    private $employee;
    private $attendanceData;
    
    public function __construct($employee, $attendanceData)
    {
        $this->employee = $employee;
        $this->attendanceData = $attendanceData;
    }

    public function calculateTimeDifference($data) 
    {
        // Convert strings to Carbon instances
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
       
        $totalExpectedWorkingHours = $this->attendanceData['workingDays'] * $hoursPerDay;

        $salaryPerHour = ($this->employee->salary / $this->attendanceData['monthDays']) / $hoursPerDay;

        $regularPay = $this->attendanceData['totalHoursWorked'] * $salaryPerHour;
        $holidayPay = $this->attendanceData['totalHolidayHoursWorked'] * $salaryPerHour * $this->employee->type->holiday_ratio;
        $overtimePay = ($this->attendanceData['totalOvertimeMinutes'] / 60) * $this->employee->type->overtime_ratio * $salaryPerHour;
        
        $lateMinutes = array_sum($this->attendanceData['lateMinutes']);
        $lateCutAmount = ($lateMinutes / 60) * $salaryPerHour;
        // $lateCutAmount = number_format(($lateMinutes / 60) * $salaryPerHour, 0);
            //   dd($lateCutAmount);
        return [
            'actualSalaryEarned' => ($regularPay + $holidayPay + $overtimePay) - $lateCutAmount,
            
            
            'totalExpectedWorkingHours' => number_format($this->attendanceData['workingDays'] * 10, 2),
            'totalOverTimeHoursWorked' => $this->attendanceData['totalOvertimeMinutes'] / 60,
            'totalOvertimeMinutes' => $this->attendanceData['totalOvertimeMinutes'],
            'totalOvertimeMinutesArray' => $this->attendanceData['overMinutes'],
            'totalOvertimePay' => number_format($overtimePay, 2, '.', ''),

            // 'actualSalaryEarned' => number_format($regularPay + $holidayPay + $overtimePay - $lateCutAmount),

            'salaryPerHour' => $salaryPerHour
        ];
    }

    
}
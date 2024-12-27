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
    private $period;
    
    public function __construct($employee, $attendanceData,$period)
    {
        $this->employee = $employee;
        $this->attendanceData = $attendanceData;
        $this->period = $period;
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
        $salaryPerHour = ($this->employee->salary / $this->attendanceData['monthDays']) / $hoursPerDay;

        $regularPay = $this->attendanceData['totalHoursWorked'] * $salaryPerHour;
        
        $holidayPay = $this->attendanceData['totalHolidayHoursWorked'] * $salaryPerHour * $this->employee->type->holiday_ratio;

        $overtimePay = ($this->attendanceData['totalOvertimeMinutes'] / 60) * $this->employee->type->overtime_ratio * $salaryPerHour;
        
        $lateMinutes = array_sum($this->attendanceData['lateMinutes']);
        $lateCutAmount = ($lateMinutes / 60) * $salaryPerHour;
        
        $normalHolidayPay = $this->attendanceData['holidayDays'] * $salaryPerHour * $hoursPerDay;

        $gazattePay = ($this->attendanceData['gazatteMinutes'] / 60) * $this->employee->type->holiday_ratio * $salaryPerHour;

        $actualSalary = ($regularPay + $holidayPay + $overtimePay+$normalHolidayPay) - $lateCutAmount;

        if ($this->period === 'first_half' || $this->period === 'second_half') {
            $actualSalary = $actualSalary / 2;
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
        ];
    }

    
}
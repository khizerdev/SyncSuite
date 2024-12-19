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

    public function calculateSalary()
    {
        $hoursPerDay = 10;
        $totalExpectedWorkingHours = $this->attendanceData['workingDays'] * $hoursPerDay;
        $salaryPerHour = $this->employee->salary / $totalExpectedWorkingHours;

        $regularPay = $this->attendanceData['totalHoursWorked'] * $salaryPerHour;
        $holidayPay = $this->attendanceData['totalHolidayHoursWorked'] * $salaryPerHour * $this->employee->type->holiday_ratio;
        $overtimePay = ($this->attendanceData['totalOvertimeMinutes'] / 60) * $this->employee->type->overtime_ratio * $salaryPerHour;

        return [
            'actualSalaryEarned' => number_format($regularPay + $holidayPay + $overtimePay, 2, '.', ''),
            'totalExpectedWorkingHours' => number_format($this->attendanceData['workingDays'] * 10, 2),
            'totalOverTimeHoursWorked' => $this->attendanceData['totalOvertimeMinutes'] / 60,
            'totalOvertimePay' => number_format($overtimePay, 2, '.', ''),
            'salaryPerHour' => $salaryPerHour
        ];
    }
}
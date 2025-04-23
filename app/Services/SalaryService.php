<?php

namespace App\Services;

use App\Models\Missscan;
use Carbon\Carbon;

class SalaryService
{
    private $employee;
    private $attendanceData;
    private $period;
    private $currentMonth;
    private $monthDays;
    private $shift;
    private $isNightShift;
    private $holidays;

    public function __construct($employee, $attendanceData, $period, $currentMonth)
    {
        $this->employee = $employee;
        $this->attendanceData = $attendanceData;
        $this->period = $period;
        $this->currentMonth = $currentMonth;
        $this->shift = $employee->timings;
        $this->isNightShift = Carbon::parse($this->shift->start_time)->greaterThan(Carbon::parse($this->shift->end_time));
        $this->monthDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, now()->year);
        $this->holidays = array_map('trim', explode(',', $employee->type->holidays));
    }

    public function calculateTimeDifference($data)
    {
        $startTime = Carbon::parse($data['start_time']);
        $endTime = !$this->isNightShift ? Carbon::parse($data['end_time']) : Carbon::parse($data['end_time'])->copy()->addDay();

        $diffInHours = $startTime->diffInHours($endTime);
        $diffInMinutes = $startTime->diffInMinutes($endTime) % 60;

        // format difference
        $difference = [
            'hours' => $diffInHours,
            'minutes' => $diffInMinutes,
            'total_minutes' => $startTime->diffInMinutes($endTime),
            'formatted' => $diffInHours,
        ];

        return $difference;
    }

    public function calculateSalary()
    {
        $totalHoursWorked = $this->attendanceData['totalMinutesWorked'] / 60;

        $gazatteDates = [];
        foreach ($this->attendanceData['gazatteHolidays']->toArray() as $gazatteDate) {
            // dd(Carbon::parse($gazatteDate["holiday_date"])->format('Y-m-d'));
            array_push($gazatteDates, Carbon::parse($gazatteDate['holiday_date'])->format('Y-m-d'));
        }

        $originalWorkingMinutes = 0;
        foreach ($this->attendanceData['groupedAttendances'] as $date => $value) {
            if (!in_array(Carbon::parse($date)->format('l'), $this->holidays) && !in_array(Carbon::parse($date)->format('Y-m-d'), $gazatteDates) && !empty($value[0])) {
                if ($value) {
                    $originalWorkingMinutes += $value[0]['dailyMinutes'];
                }
            }
        }

        $totalHoursWorked = $originalWorkingMinutes / 60;

        $timings = $this->calculateTimeDifference($this->employee->timings);
        $hoursPerDay = intval($timings['formatted']);
        $salaryPerHour = $this->employee->salary / $this->monthDays / $hoursPerDay;
        // dd($this->attendanceData['gazatteHolidays']);
        $regularPay = $totalHoursWorked * $salaryPerHour;

        $minutesWorkedInHoliday = 0;
        foreach ($this->attendanceData['dailyMinutes'] as $date => $value) {
            if (in_array(Carbon::parse($date)->format('l'), $this->holidays)) {
                $minutesWorkedInHoliday += $value;
            }
        }

        $totalHolidayMinutesWorked = $this->attendanceData['totalHolidayMinutesWorked'];

        // $holidayPay = ($totalHolidayMinutesWorked/60) * $salaryPerHour * $this->employee->type->holiday_ratio;

        $holidayWorkingMinutes = 0;
        $holidayOverMins = 0;
        foreach ($this->attendanceData['groupedAttendances'] as $date => $value) {
            if (in_array(Carbon::parse($date)->format('l'), $this->holidays) || (in_array(Carbon::parse($date)->format('Y-m-d'), $gazatteDates) && !empty($value[0]))) {
                if ($value) {
                    // $holidayWorkingMinutes += $value[0]['dailyMinutes']+$value[0]['overMinutes']+$value[0]['earlyMinutes'];
                    $holidayWorkingMinutes += $value[0]['dailyMinutes'];
                    $holidayOverMins += $value[0]['overMinutes'];
                }
            }
        }

        // dd($holidayWorkingMinutes);

        $holidayPay = ($holidayWorkingMinutes / 60) * $salaryPerHour * $this->employee->type->holiday_ratio;

        $sandWhichViolations = $this->countSandwichRuleViolations($this->attendanceData['groupedAttendances'], $this->attendanceData['gazatteHolidays']);

        $overMintuesWithoutHoliday = 0;

        foreach ($this->attendanceData['overMinutes'] as $date => $value) {
            if (!in_array(Carbon::parse($date)->format('l'), $this->holidays) && !in_array(Carbon::parse($date)->format('Y-m-d'), $gazatteDates)) {
                $overMintuesWithoutHoliday += $value;
            }
        }

        foreach ($this->attendanceData['overMinutes'] as $date => $value) {
            if (in_array(Carbon::parse($date)->format('l'), $this->holidays)) {
                $this->attendanceData['overMinutes'][$date] = 0;
            }
        }

        $overtimePay = ($overMintuesWithoutHoliday / 60) * $this->employee->type->overtime_ratio * $salaryPerHour;

        $lateMinutes = array_sum($this->attendanceData['lateMinutes']);
        // dd($lateMinutes);
        $lateCutAmount = ($lateMinutes / 60) * $salaryPerHour;

        $holidayWorkedDays = 0;
        foreach ($this->attendanceData['dailyMinutes'] as $date => $value) {
            if (in_array(Carbon::parse($date)->format('l'), $this->holidays) && $value > 0) {
                $holidayWorkedDays += 1;
            }
        }
        // dd($this->attendanceData['holidayDays']);
        // dd($holidayWorkedDays);
        $normalHolidayPay = ($this->attendanceData['holidayDays'] - $holidayWorkedDays) * $salaryPerHour * $hoursPerDay;
        // dd($normalHolidayPay);

        $gazatteDaysWithoutWorked = 0;
        foreach ($this->attendanceData['dailyMinutes'] as $date => $value) {
            if (in_array(Carbon::parse($date)->format('Y-m-d'), $gazatteDates) && !in_array(Carbon::parse($date)->format('l'), $this->holidays) && $value == 0) {
                $gazatteDaysWithoutWorked += 1;
            }
        }
        // foreach ($this->attendanceData['groupedAttendances'] as $date => $value) {
        //     dd($this->attendanceData['groupedAttendances']);
        //     if (in_array(Carbon::parse($date)->format('Y-m-d'),$gazatteDates) && empty($value)) {

        //         // dd($this->attendanceData['dailyMinutes'][$date]);
        //         $gazatteDaysWithoutWorked += 1;
        //     }
        // }

        // dd($gazatteDaysWithoutWorked);

        $gazattePay = $gazatteDaysWithoutWorked * $hoursPerDay * $salaryPerHour;
        // dd($this->attendanceData['gazatteMinutes']);

        $missScanCount = $this->attendanceData['missScanCount'];
        $missScanCleared = Missscan::where('employee_id', $this->employee->id)->where('month', $this->attendanceData['month'])->where('year', $this->attendanceData['year'])->where('duration', $this->period)->first();

        $actualSalary = $regularPay + $holidayPay + $normalHolidayPay + $gazattePay;

        // $earlyOutCutAmount = $this->attendanceDate['earlyCheckoutMinutes'];

        $earlyOutMins = array_sum($this->attendanceData['earlyCheckoutMinutes']);
        // dd($lateMinutes);
        $earlyOutCutAmount = ($earlyOutMins / 60) * $salaryPerHour;

        if (!$this->employee->type->adjust_hours) {
            $actualSalary += $overtimePay;
        } else {
            $check = $overtimePay - $lateCutAmount - $earlyOutCutAmount >= 0 ? ($actualSalary += $lateCutAmount + $earlyOutCutAmount) : ($actualSalary += $overtimePay);
            // dd($actualSalary,$lateCutAmount);
        }

        $missDeductDays = 0;
        $missAmount = 0;
        $missScanPerDayAmount = 0;

        $missDaysAmount = 0;

        if ($missScanCleared) {
            $missScanPerDayAmount = $this->employee->salary / $this->monthDays;
            $dayRatio = (int) floor($missScanCount / 3); // 0.33 => 0

            $missAmount = ($missScanCount - $dayRatio) * $missScanPerDayAmount;
            $missDeductDays = $missScanCount;

            $actualSalary += $missAmount;
            $missDaysAmount = $missScanCount * $missScanPerDayAmount - $missAmount;
        }

        $perDayAmount = $this->employee->salary / $this->monthDays;
        if ($sandWhichViolations > 0) {
            $sanwichDeductedAmount = $perDayAmount * $sandWhichViolations;
            $actualSalary -= $sanwichDeductedAmount;
        }

        $workedDays = 0;
        foreach ($this->attendanceData['groupedAttendances'] as $date => $value) {
            if (!empty($value)) {
                // dd($this->attendanceData['dailyMinutes'][$date]);
                $workedDays += 1;
            }
        }
        // foreach ($this->attendanceData['dailyMinutes'] as $date => $value) {

        //     if (in_array(Carbon::parse($date)->format('Y-m-d'),$gazatteDates) && $value ==  0) {
        //         $gazatteDaysWithoutWorked += 1;
        //     }
        // }

        // dd($this->attendanceData['workingDays']);
        // dd($hoursPerDay);
        return [
            'actualSalaryEarned' => $actualSalary,
            'salaryPerHour' => $salaryPerHour,
            'totalBaseSalary' => $this->employee->salary,
            'totalAdjustedSalary' => $actualSalary,

            'holidayHours' => number_format($holidayWorkingMinutes / 60, 2),
            'totalExpectedWorkingHours' => number_format($this->attendanceData['workingDays'] * $hoursPerDay, 2),
            // 'totalHoursWorked'          => $this->attendanceData['totalMinutesWorked'] / 60,
            'totalHoursWorked' => number_format($originalWorkingMinutes / 60, 2),
            'totalWorkingDays' => $this->attendanceData['workingDays'],
            'totalWorkedDays' => $workedDays,
            // 'totalPresentDays'          => $this->attendanceData['presentDays'],
            // 'totalAbsentDays'           => $this->attendanceData['workingDays'] - $this->attendanceData['presentDays'],

            'totalOverTimeHoursWorked' => array_sum($this->attendanceData['overMinutes']) / 60,
            'totalOvertimeMinutes' => number_format($overMintuesWithoutHoliday, 2),
            'totalOvertimeMinutesArray' => $this->attendanceData['overMinutes'],
            'totalOvertimePay' => number_format($overtimePay, 2, '.', ''),
            'overtimeEligibility' => $overMintuesWithoutHoliday > 0 ? 'Eligible' : 'Not Eligible',

            'holidayPay' => number_format($holidayPay, 2),
            'normalHolidayPay' => $normalHolidayPay,
            'holidayOverMins' => $holidayOverMins,
            'gazattePay' => number_format($gazattePay, 2),
            'gazatteHolidays' => $this->attendanceData['gazatteHolidays'],
            'totalHolidayHoursWorked' => $this->attendanceData['totalHolidayHoursWorked'],
            'totalHolidayDays' => $this->attendanceData['holidayDays'],

            'earlyOutCutAmount' => $earlyOutCutAmount,
            'lateCutAmount' => $lateCutAmount,
            'totalLateMinutes' => number_format($lateMinutes, 2),
            'totalLateDays' => count($this->attendanceData['lateMinutes']),
            'missDeductDays' => $missDeductDays,
            'missAmount' => $missDaysAmount,
            'missScanCount' => $missScanCount,
            'missScanCleared' => $missScanCleared ? 'Yes' : 'No',
            'sandwichDeduct' => $sandWhichViolations . ' Days - Amount ' . $perDayAmount * $sandWhichViolations,
            'totalSandwichViolations' => $sandWhichViolations,

            // 'attendancePercentage'      => number_format(($this->attendanceData['presentDays'] / $this->attendanceData['workingDays']) * 100, 2) . '%',
            // 'effectiveHourlyRate'       => number_format($actualSalary / ($this->attendanceData['totalMinutesWorked'] / 60), 2),
            'totalDeductions' => $lateCutAmount + $missDaysAmount + $perDayAmount * $sandWhichViolations,
            'netSalaryAfterDeductions' => $actualSalary - ($lateCutAmount + $missDaysAmount + $perDayAmount * $sandWhichViolations),
        ];
    }

    public function countSandwichRuleViolations($groupedAttendances, $gazatteHolidays)
    {
        $violations = 0;
        $dates = array_keys($groupedAttendances);
        $gazetteHolidayDates = $gazatteHolidays->pluck('holiday_date')->toArray();

        $gazatteDates = array_map(function ($item) {
            return $item->toDateString();
        }, $gazetteHolidayDates);

        $counter = 0;
        foreach ($dates as $date) {

            $counter++;
            if ($counter == 1) {
                continue;
            }
            // Check previous day
            $prevDay = date('Y-m-d', strtotime($date . ' -1 day'));
            $prevDayIsAbsent = isset($groupedAttendances[$prevDay]) && count($groupedAttendances[$prevDay]) < 1 && !in_array($prevDay, $gazatteDates) && !in_array(date('l', strtotime($prevDay)), $this->holidays);

            // Check next day
            $nextDay = date('Y-m-d', strtotime($date . ' +1 day'));
            $nextDayIsAbsent = isset($groupedAttendances[$nextDay]) && count($groupedAttendances[$nextDay]) < 1 && !in_array($nextDay, $gazatteDates) && !in_array(date('l', strtotime($nextDay)), $this->holidays);

            // Check if the middle day (current day) is a holiday
            $middleDayIsHoliday = in_array($date, $gazatteDates) || in_array(date('l', strtotime($date)), $this->holidays);
            // If 3 consecutive days are absent and the middle day is a holiday, increment violations
            if ($prevDayIsAbsent && $middleDayIsHoliday && $nextDayIsAbsent) {
                $violations += 1;
            }
        }
        return $violations;
    }
}

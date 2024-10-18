<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeePayrollService
{
    public function calculateAttendance($employeeId)
    {
        $calculator = new EmployeeAttendanceCalculator($employeeId);
        return $calculator->calculateAttendance();
    }
}

class EmployeeAttendanceCalculator
{
    private $employee;
    private $shift;
    private $holidays;
    private $holidayRatio;
    private $overTimeRatio;
    private $attendances;
    private $isNightShift;

    public function __construct($employeeId)
    {
        $this->employee = Employee::findOrFail($employeeId);
        $this->shift = $this->employee->timings;
        $this->setHolidays();
        $this->setRatios();
        $this->attendances = $this->getAttendances();
        $this->isNightShift = $this->checkIfNightShift();
    }

    private function setHolidays()
    {
        $this->holidays = explode(',', $this->employee->type->holidays);
        $this->holidays = array_map('trim', $this->holidays);
    }

    private function setRatios()
    {
        $adjustment = $this->employee->type->adjustment;
        $this->holidayRatio = $adjustment == 1 ? 0 : $this->employee->type->holiday_ratio ?? 1;
        $this->overTimeRatio = $adjustment == 1 ? 0 : $this->employee->type->overtime_ratio ?? 1;
    }

    private function getAttendances()
    {
        return Attendance::where('code', $this->employee->code)
            ->orderBy('datetime')
            ->get();
    }

    private function checkIfNightShift()
    {
        return Carbon::parse($this->shift->start_time)->greaterThan(Carbon::parse($this->shift->end_time));
    }

    public function calculateAttendance()
    {
        $groupedAttendances = $this->groupAttendances();
        $workingDays = $this->calculateWorkingDays();
        $salaryPerHour = $this->calculateSalaryPerHour($workingDays);

        $attendanceData = $this->processAttendances($groupedAttendances);

        return $this->calculateFinalResults($attendanceData, $salaryPerHour, $workingDays);
    }

    private function groupAttendances()
    {
        $groupedAttendances = [];
        $startDate = Carbon::create(2024, 8, 1);
        $endDate = Carbon::create(2024, 8, 31);

        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');
            $groupedAttendances[$date] = [];
            $startDate->addDay();
        }

        return $groupedAttendances;
    }

    private function calculateWorkingDays()
    {
        $startDate = Carbon::create(2024, 8, 1);
        $endDate = Carbon::create(2024, 8, 31);
        $workingDays = 0;

        while ($startDate->lte($endDate)) {
            if (!in_array($startDate->format('l'), $this->holidays)) {
                $workingDays++;
            }
            $startDate->addDay();
        }

        return $workingDays;
    }

    private function calculateSalaryPerHour($workingDays)
    {
        $hoursPerDay = 12;
        $totalExpectedWorkingHours = $workingDays * $hoursPerDay;
        return $this->employee->salary / $totalExpectedWorkingHours;
    }

    private function processAttendances($groupedAttendances)
    {
        $totalMinutesWorked = 0;
        $totalHolidayMinutesWorked = 0;
        $totalOvertimeMinutes = 0;

        foreach ($this->attendances as $i => $attendance) {
            $checkIn = Carbon::parse($attendance->datetime);
            $date = $checkIn->format('Y-m-d');

            $checkOut = $this->findCheckOut($i);

            $calculationTimes = $this->adjustCalculationTimes($checkIn, $checkOut);

            $groupedAttendances[$date][] = [
                'original_checkin' => $checkIn,
                'original_checkout' => $checkOut,
                'calculation_checkin' => $calculationTimes['checkin'],
                'calculation_checkout' => $calculationTimes['checkout'],
                'is_incomplete' => !$checkOut
            ];
        }

        foreach ($groupedAttendances as $date => $entries) {
            $result = $this->calculateDailyMinutes($date, $entries);
            $totalMinutesWorked += $result['totalMinutes'];
            $totalHolidayMinutesWorked += $result['holidayMinutes'];
            $totalOvertimeMinutes += $result['overtimeMinutes'];
        }

        return [
            'totalMinutesWorked' => $totalMinutesWorked,
            'totalHolidayMinutesWorked' => $totalHolidayMinutesWorked,
            'totalOvertimeMinutes' => $totalOvertimeMinutes
        ];
    }

    private function findCheckOut($currentIndex)
    {
        $checkIn = Carbon::parse($this->attendances[$currentIndex]->datetime);
        $shiftEnd = Carbon::parse($this->shift->end_time);
        if ($this->isNightShift) {
            $shiftEnd->addDay();
        }
        $maxCheckOut = $shiftEnd->copy()->addHours(4);

        for ($j = $currentIndex + 1; $j < count($this->attendances); $j++) {
            $nextEntry = Carbon::parse($this->attendances[$j]->datetime);
            if ($nextEntry <= $maxCheckOut && abs($nextEntry->diffInHours($checkIn)) <= 16) {
                return $nextEntry;
            }
        }

        return null;
    }

    private function adjustCalculationTimes($checkIn, $checkOut)
    {
        if ($this->isNightShift) {
            return [
                'checkin' => $checkIn->copy()->addHours(6),
                'checkout' => $checkOut ? $checkOut->copy()->addHours(6) : null
            ];
        }

        return [
            'checkin' => $checkIn,
            'checkout' => $checkOut
        ];
    }

    private function calculateDailyMinutes($date, $entries)
    {
        $shiftStartTime = Carbon::parse($this->shift->start_time)->addHours($this->isNightShift ? 6 : 0)->format('H:i:s');
        $shiftEndTime = Carbon::parse($this->shift->end_time)->addHours($this->isNightShift ? 6 : 0)->format('H:i:s');

        $shiftStart = Carbon::parse($date . ' ' . $shiftStartTime);
        $shiftEnd = Carbon::parse($date . ' ' . $shiftEndTime);

        if ($this->isNightShift) {
            $shiftEnd->addDay();
        }

        $totalMinutes = 0;
        $overtimeMinutes = 0;
        $holidayMinutes = 0;

        foreach ($entries as $entry) {
            if (!$entry['is_incomplete']) {
                $entryTimeStart = $entry['calculation_checkin'];
                $entryTimeEnd = $entry['calculation_checkout'];

                $startTime = $entryTimeStart->max($shiftStart);
                $endTime = $entryTimeEnd->min($shiftEnd);

                if ($startTime->lt($endTime)) {
                    $minutesWorked = $startTime->diffInMinutes($endTime);
                    $totalMinutes += $minutesWorked;

                    $dayOfWeek = Carbon::parse($date)->format('l');
                    if (in_array($dayOfWeek, $this->holidays)) {
                        $holidayMinutes += $minutesWorked;
                    }

                    $workedMinutes = $entryTimeStart->diffInMinutes($entryTimeEnd);
                    if ($workedMinutes > 720) {
                        $overtimeMinutes += $workedMinutes - 720;
                    }
                }
            }
        }

        return [
            'totalMinutes' => $totalMinutes,
            'overtimeMinutes' => $overtimeMinutes,
            'holidayMinutes' => $holidayMinutes
        ];
    }

    private function calculateFinalResults($attendanceData, $salaryPerHour, $workingDays)
    {
        $totalHoursWorked = $attendanceData['totalMinutesWorked'] / 60;
        $totalHolidayHoursWorked = $attendanceData['totalHolidayMinutesWorked'] / 60;
        $totalOvertimeHours = $attendanceData['totalOvertimeMinutes'] / 60;

        $regularHoursWorked = $totalHoursWorked;
        $overtimeAmount = $totalOvertimeHours * ($this->overTimeRatio * $salaryPerHour);
        $actualSalaryEarned = ($regularHoursWorked * $salaryPerHour) + 
                              ($totalHolidayHoursWorked * $salaryPerHour * $this->holidayRatio) + 
                              $overtimeAmount;

        return [
            'totalExpectedWorkingDays' => number_format($workingDays * 12, 2),
            'totalHoursWorked' => number_format($totalHoursWorked, 2),
            'totalHolidayHoursWorked' => number_format($totalHolidayHoursWorked, 2),
            'totalOvertimeHours' => number_format($totalOvertimeHours, 2),
            'overtimeAmount' => number_format($overtimeAmount, 2),
            'actualSalaryEarned' => number_format($actualSalaryEarned, 2)
        ];
    }
}
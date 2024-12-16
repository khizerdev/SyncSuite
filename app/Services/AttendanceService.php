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

class AttendanceService
{
    private $employee;
    private $shift;
    private $holidays;
    private $holidayRatio;
    private $overTimeRatio;
    private $isNightShift;
    
    public function __construct($employee)
    {
        $this->employee = $employee;
        $this->shift = $employee->timings;
        $this->holidays = array_map('trim', explode(',', $employee->type->holidays));
        $this->holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 1;
        $this->overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 1;
        $this->isNightShift = Carbon::parse($this->shift->start_time)->greaterThan(Carbon::parse($this->shift->end_time));
    }

    public function processAttendance($startDate, $endDate)
    {
        $userInfo = $this->getUserInfo();
        if (!$userInfo) {
            return null;
        }

        $attendances = $this->getAttendances($userInfo->id, $startDate, $endDate);
        $dates = $this->initializeDates($startDate, $endDate);
        $groupedAttendances = $dates['groupedAttendances'];
        $workingDays = $dates['workingDays'];

        $processedAttendances = $this->processAttendanceRecords($attendances, $groupedAttendances);
        $calculatedMinutes = $this->calculateWorkingMinutes($processedAttendances['groupedAttendances']);

        return [
            'employee' => $this->employee,
            'dailyMinutes' => $calculatedMinutes['dailyMinutes'],
            'totalHoursWorked' => $calculatedMinutes['totalMinutesWorked'] / 60,
            'workingDays' => $workingDays,
            'totalHolidayHoursWorked' => $calculatedMinutes['totalHolidayMinutesWorked'] / 60,
            'holidays' => $this->holidays,
            'totalOvertimeMinutes' => $calculatedMinutes['totalOvertimeMinutes'],
            'isNightShift' => $this->isNightShift,
            'shift' => $this->shift,
            'groupedAttendances' => $processedAttendances['groupedAttendances'],
        ];
    }

    private function getUserInfo()
    {
        return UserInfo::where('code', $this->employee->code)->first();
    }

    private function getAttendances($userId, $startDate, $endDate)
    {
        return Attendance::where('code', $userId)
            ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();
    }

    private function initializeDates($startDate, $endDate)
    {
        $groupedAttendances = [];
        $workingDays = 0;
        // $currentDate = clone $startDate;
        $currentDate = Carbon::parse($startDate);

        while ($currentDate->lte($endDate)) {
            $date = $currentDate->format('Y-m-d');
            $groupedAttendances[$date] = [];
            
            if (!in_array($currentDate->format('l'), $this->holidays)) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return [
            'groupedAttendances' => $groupedAttendances,
            'workingDays' => $workingDays
        ];
    }

    private function processAttendanceRecords($attendances, $groupedAttendances)
    {
        for ($i = 0; $i < count($attendances); $i++) {
            $checkIn = Carbon::parse($attendances[$i]->datetime);
            $date = $checkIn->format('Y-m-d');
            
            if (!empty($groupedAttendances[$date])) {
                continue;
            }

            $currentDateEntries = $this->getCurrentDateEntries($attendances, $i, $date);
            if (count($currentDateEntries) > 0) {
                $checkInOut = $this->determineCheckInOut($currentDateEntries);
                $groupedAttendances[$date][] = $this->createAttendanceEntry($checkInOut['checkIn'], $checkInOut['checkOut']);
                $i = $currentDateEntries['lastIndex'] - 1;
            }
        }

        return ['groupedAttendances' => $groupedAttendances];
    }

    private function getCurrentDateEntries($attendances, $startIndex, $targetDate)
    {
        $entries = [];
        $currentIndex = $startIndex;

        while ($currentIndex < count($attendances)) {
            $entry = Carbon::parse($attendances[$currentIndex]->datetime);
            if ($entry->format('Y-m-d') == $targetDate) {
                $entries[] = $attendances[$currentIndex];
                $currentIndex++;
            } else {
                break;
            }
        }

        return [
            'entries' => $entries,
            'lastIndex' => $currentIndex
        ];
    }

    private function determineCheckInOut($dateEntries)
    {
        $entries = $dateEntries['entries'];
        $entriesCount = count($entries);
        
        if ($entriesCount == 0) {
            return ['checkIn' => null, 'checkOut' => null];
        }

        $checkIn = Carbon::parse($entries[0]->datetime);
        $checkOut = null;

        if ($entriesCount > 1) {
            $checkOut = Carbon::parse($entries[$entriesCount - ($entriesCount % 2 == 0 ? 1 : 2)]->datetime);
        }

        // Validate checkout time
        $shiftEnd = Carbon::parse($this->shift->end_time);
        if ($this->isNightShift) {
            $shiftEnd->addDay();
        }
        $maxCheckOut = $shiftEnd->copy()->addHours(4);

        if ($checkOut && $checkOut > $maxCheckOut) {
            $checkOut = null;
        }

        return ['checkIn' => $checkIn, 'checkOut' => $checkOut];
    }

    private function createAttendanceEntry($checkIn, $checkOut)
    {
        if ($this->isNightShift) {
            $calculationCheckIn = $checkIn ? $checkIn->copy()->addHours(6) : null;
            $calculationCheckOut = $checkOut ? $checkOut->copy()->addHours(6) : null;
        } else {
            $calculationCheckIn = $checkIn;
            $calculationCheckOut = $checkOut;
        }

        return [
            'original_checkin' => $checkIn,
            'original_checkout' => $checkOut,
            'calculation_checkin' => $calculationCheckIn,
            'calculation_checkout' => $calculationCheckOut,
            'is_incomplete' => !$checkOut
        ];
    }

    private function calculateWorkingMinutes($groupedAttendances)
    {
        $totalMinutesWorked = 0;
        $totalHolidayMinutesWorked = 0;
        $totalOvertimeMinutes = 0;
        $dailyMinutes = [];

        foreach ($groupedAttendances as $date => $entries) {
            $shiftTimes = $this->getShiftTimes($date);
            $results = $this->calculateDailyMinutes($entries, $shiftTimes, $date);
            
            $dailyMinutes[$date] = $results['totalMinutes'];
            $totalMinutesWorked += $results['totalMinutes'];
            $totalHolidayMinutesWorked += $results['holidayMinutes'];
            $totalOvertimeMinutes += $results['overtimeMinutes'];
        }

        return [
            'dailyMinutes' => $dailyMinutes,
            'totalMinutesWorked' => $totalMinutesWorked,
            'totalHolidayMinutesWorked' => $totalHolidayMinutesWorked,
            'totalOvertimeMinutes' => $totalOvertimeMinutes
        ];
    }

    private function getShiftTimes($date)
    {
        $shiftStartTime = Carbon::parse($this->shift->start_time)
            ->addHours($this->isNightShift ? 6 : 0)
            ->format('H:i:s');
        $shiftEndTime = Carbon::parse($this->shift->end_time)
            ->addHours($this->isNightShift ? 6 : 0)
            ->format('H:i:s');

        $shiftStart = Carbon::parse($date . ' ' . $shiftStartTime);
        $shiftEnd = Carbon::parse($date . ' ' . $shiftEndTime);

        if ($this->isNightShift) {
            $shiftEnd->addDay();
        }

        return ['start' => $shiftStart, 'end' => $shiftEnd];
    }

    private function calculateDailyMinutes($entries, $shiftTimes, $date)
    {
        $totalMinutes = 0;
        $holidayMinutes = 0;
        $overtimeMinutes = 0;

        foreach ($entries as $entry) {
            if (!$entry['is_incomplete']) {
                $minutes = $this->calculateEntryMinutes($entry, $shiftTimes);
                $totalMinutes += $minutes['worked'];
                
                if (in_array(Carbon::parse($date)->format('l'), $this->holidays)) {
                    $holidayMinutes += $minutes['worked'];
                }
                
                $overtimeMinutes += $minutes['overtime'];
            }
        }

        return [
            'totalMinutes' => $totalMinutes,
            'holidayMinutes' => $holidayMinutes,
            'overtimeMinutes' => $overtimeMinutes
        ];
    }

    private function calculateEntryMinutes($entry, $shiftTimes)
    {
        $startTime = $entry['calculation_checkin']->max($shiftTimes['start']);
        $endTime = $entry['calculation_checkout']->min($shiftTimes['end']);

        if ($startTime->lt($endTime)) {
            $minutesWorked = $startTime->diffInMinutes($endTime);
            $totalWorkedMinutes = $entry['calculation_checkin']->diffInMinutes($entry['calculation_checkout']);
            
            $overtimeMinutes = $totalWorkedMinutes > 600 ? $totalWorkedMinutes - 600 : 0;

            return [
                'worked' => $minutesWorked,
                'overtime' => $overtimeMinutes
            ];
        }

        return ['worked' => 0, 'overtime' => 0];
    }
}

<?php

namespace App\Services;

use App\Models\AdvanceSalary;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\GazetteHoliday;
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
    private $gazatteHolidays;
    
    public function __construct($employee)
    {
        $this->employee = $employee;
        $this->shift = $employee->timings;
        $this->holidays = array_map('trim', explode(',', $employee->type->holidays));
        $this->holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 1;
        $this->overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 1;
        $this->isNightShift = Carbon::parse($this->shift->start_time)->greaterThan(Carbon::parse($this->shift->end_time));
    }

    public function getGazatteHolidays($startDate, $endDate)
    {
        $holidays = GazetteHoliday::whereBetween('holiday_date', [$startDate, $endDate])
            ->get(['holiday_date']);

        return $holidays;
    }

    public function processAttendance($startDate, $endDate)
    {
        $userInfo = $this->getUserInfo();
        if (!$userInfo) {
            return null;
        }
        $gazetteHolidays = $this->getGazatteHolidays($startDate,$endDate);
        $attendances = $this->getAttendances($userInfo->id, $startDate, $endDate);
        // dd($endDate);
        $dates = $this->initializeDates($startDate, $endDate);
        $groupedAttendances = $dates['groupedAttendances'];
        $workingDays = $dates['workingDays'];
        $monthDays = $dates['monthDays'];
        $holidayDays = $dates['holidayDays'];

        $processedAttendances = $this->processAttendanceRecords($attendances, $groupedAttendances);
        $calculatedMinutes = $this->calculateWorkingMinutes($processedAttendances['groupedAttendances'],$gazetteHolidays);

        $missScanCount = $this->getMissScanCount($processedAttendances['groupedAttendances']);
        // dd($calculatedMinutes['totalHolidayMinutesWorked']);
        $actualHoursWorked = ($calculatedMinutes['totalMinutesWorked'] / 60) - ($calculatedMinutes['totalHolidayMinutesWorked'] / 60) - ($calculatedMinutes['gazatteMinutes'] /60 );
        
        $gazatteDates = [];
        
        foreach($gazetteHolidays->toArray() as $gazatteDate){
            
            if(!in_array(Carbon::parse($gazatteDate["holiday_date"])->format('l'),$this->holidays)){
                
                array_push($gazatteDates,Carbon::parse($gazatteDate["holiday_date"])->format('Y-m-d'));
            }
        }
        
        
        foreach ($processedAttendances['groupedAttendances'] as $date => &$entries) {
            $dailyMinutes = 0;
            foreach ($entries as &$entry) {
                if (!$entry['is_incomplete']) {
                    $entryStart = $entry['calculation_checkin'];
                    $entryEnd = $entry['calculation_checkout'];
                    $dailyMinutes += $entryStart->diffInMinutes($entryEnd) - $calculatedMinutes['earlyCheckinMinutes'][$date] - $calculatedMinutes['overMinutes'][$date];
                }
            }
            // Add dailyMinutes to each entry for the date
            foreach ($entries as &$entry) {
                $entry['dailyMinutes'] = $dailyMinutes;
                $entry['overMinutes'] = $calculatedMinutes['overMinutes'][$date];
                $entry['earlyMinutes'] = $calculatedMinutes['earlyCheckinMinutes'][$date];
            }
        }
        // dd($processedAttendances['groupedAttendances']);
        return [
            'employee' => $this->employee,
            
            'shift' => $this->shift,
            'isNightShift' => $this->isNightShift,
        
            'dailyMinutes' => $calculatedMinutes['dailyMinutes'],
            'earlyCheckinMinutes' => $calculatedMinutes['earlyCheckinMinutes'],
            'lateMinutes' => $calculatedMinutes['lateMinutes'],
            'overMinutes' => $calculatedMinutes['overMinutes'],
            'totalMinutesWorked' => $calculatedMinutes['totalMinutesWorked'],
            'totalWorkingHours' => $calculatedMinutes['totalMinutesWorked'] / 60, // Insightful
            'totalOvertimeMinutes' => $calculatedMinutes['totalOvertimeMinutes'],
            'totalOvertimeHours' => $calculatedMinutes['totalOvertimeMinutes'] / 60, // Insightful
            'actualHoursWorked' => ($calculatedMinutes['totalMinutesWorked'] / 60) - ($calculatedMinutes['totalHolidayMinutesWorked'] / 60),
            'averageDailyWorkingHours' => ($calculatedMinutes['totalMinutesWorked'] / 60) / $workingDays, // Insightful
        
            'gazatteMinutes' => $calculatedMinutes['gazatteMinutes'],
            'gazatteHolidays' => $gazetteHolidays,
            'gazatteDates' => $gazatteDates,
            'holidayDays' => $holidayDays,
            'totalHolidayMinutesWorked' => $calculatedMinutes['totalHolidayMinutesWorked'],
            'totalHolidayHoursWorked' => $calculatedMinutes['totalHolidayMinutesWorked'] / 60,
            'holidayOvertimeHours' => $calculatedMinutes['gazatteMinutes'] / 60,
            'holidays' => $this->holidays,
        
            'groupedAttendances' => $processedAttendances['groupedAttendances'],
            'missScanCount' => $missScanCount,
            // 'attendanceAccuracy' => (($processedAttendances['groupedAttendances'] - $missScanCount) / $processedAttendances['groupedAttendances']) * 100,
        
            'workingDays' => $workingDays,
            'monthDays' => $monthDays,
            'effectiveWorkingDays' => $workingDays - $holidayDays - count($gazetteHolidays),
            'productivityRatio' => ($calculatedMinutes['totalMinutesWorked'] / 60) / ($workingDays * 8),
            'month' => $startDate instanceof Carbon ? $startDate->format('m') : Carbon::parse($startDate)->format('m'),
            'year' => $startDate instanceof Carbon ? $startDate->format('Y') : Carbon::parse($startDate)->format('Y'),
            
        ];
    }

    public function getMissScanCount($groupedAttendances){

        $missScanCount = 0;
        if (is_array($groupedAttendances)) {
            
            foreach ($groupedAttendances as $values) {
                if (!is_array($values)) {
                    continue;
                }

                foreach ($values as $value) { 
                    if (!is_array($value) || !array_key_exists("is_incomplete", $value)) {
                        continue; 
                    }
    
                    if ($value["is_incomplete"]) {
                        $missScanCount++; 
                    }
    
                    break; 
                }
            }
        }

        return $missScanCount;
    }

    private function getUserInfo()
    {
        return UserInfo::where('code', $this->employee->code)->first();
    }

    private function getAttendances($userId, $startDate, $endDate)
{
    
    if ($this->isNightShift) {
        
        $endDate = Carbon::parse($endDate)->copy()->addDays(1)->setTime(12, 0, 0)->format('Y-m-d H:i:s');
    } else {
       
        $endDate = Carbon::parse($endDate)->copy()->endOfDay()->format('Y-m-d H:i:s');
    }
  
    return Attendance::where('code', $userId)
        ->whereBetween('datetime', [$startDate, $endDate])
        ->orderBy('datetime')
        ->get();
}

    private function initializeDates($startDate, $endDate)
    {
        $groupedAttendances = [];
        $workingDays = 0;
        $holidayDays = 0;
        $monthDays = 0;
        // $currentDate = clone $startDate;
        $currentDate = Carbon::parse($startDate);

        while ($currentDate->lte($endDate)) {
            $date = $currentDate->format('Y-m-d');
            $groupedAttendances[$date] = [];
            
            if (!in_array($currentDate->format('l'), $this->holidays)) {
                $workingDays++;
            }
            if (in_array($currentDate->format('l'), $this->holidays)) {
                $holidayDays++;
            }
            $monthDays++;
            $currentDate->addDay();
        }
        return [
            'groupedAttendances' => $groupedAttendances,
            'workingDays' => $workingDays,
            'monthDays' => $monthDays,
            'holidayDays' => $holidayDays,
        ];
    }

    private function processAttendanceRecords($attendances, $groupedAttendances)
    {
        for ($i = 0; $i < count($attendances); $i++) {
            $checkIn = Carbon::parse($attendances[$i]->datetime);
            $date = $checkIn->format('Y-m-d');
            
            // Group entries by date if not already grouped
            if (!isset($groupedAttendances[$date])) {
                $groupedAttendances[$date] = [];
            }

            $currentDateEntries = $this->getCurrentDateEntries($attendances, $i, $date,$this->isNightShift);
            if (count($currentDateEntries['entries']) > 0) {
                $nestedEntries = $this->determineDailyEntries($currentDateEntries['entries']);
                foreach ($nestedEntries as $entry) {
                    $groupedAttendances[$date][] = $this->createAttendanceEntry($entry['checkIn'], $entry['checkOut'],$entry);
                    
                }
                $i = $currentDateEntries['lastIndex'] - 1;
            }
        }

        return ['groupedAttendances' => $groupedAttendances];
    }

    private function determineDailyEntries($entries)
    {
        $nestedEntries = [];
        $currentCheckIn = null;
        $lastCheckOut = null;

        foreach ($entries as $entry) {
            $entryTime = Carbon::parse($entry->datetime);

            if (!$currentCheckIn) {
                // Ensure this is not immediately after the last checkout
                if ($lastCheckOut && $lastCheckOut->diffInMinutes($entryTime) <= 2) {
                    continue;
                }
                $currentCheckIn = $entryTime;
                continue;
            }

            if ($currentCheckIn->diffInMinutes($entryTime) > 2) {
                // Treat as a checkout for the current check-in
                $nestedEntries[] = [
                    'checkIn' => $currentCheckIn,
                    'checkOut' => $entryTime
                ];
                $lastCheckOut = $entryTime; // Update last checkout time
                $currentCheckIn = null;
            }
        }

        if ($currentCheckIn) {
            $nestedEntries[] = [
                'checkIn' => $currentCheckIn,
                'checkOut' => null
            ];
        }

        return $nestedEntries;
    }


    private function getCurrentDateEntries($attendances, $startIndex, $targetDate)
    {
        // dd($attendances);
        $entries = [];
        $currentIndex = $startIndex;
        $targetDateObj = Carbon::parse($targetDate);
        $isNightShift = $this->isNightShift;
        if ($isNightShift) {

            while ($currentIndex < count($attendances)) {
                $entry = Carbon::parse($attendances[$currentIndex]->datetime);
                $entryDate = $entry->format('Y-m-d');
                $entryHour = (int)$entry->format('H');
                
                // Case 1: Entry is on target date
                if ($entryDate === $targetDate) {
                    if ($entryHour >= 18) { // After 6 PM
                        $entries[] = $attendances[$currentIndex];
                    }
                    $currentIndex++;
                    continue;
                }
                
                // Case 2: Entry is on next day
                $nextDay = $targetDateObj->copy()->addDay()->format('Y-m-d');
                if ($entryDate === $nextDay && $entryHour < 12) {
                    $entries[] = $attendances[$currentIndex];
                    $currentIndex++;
                    continue;
                }
                
                // If we reach here, we're done with this night shift
                break;
            }
        } else {
            // Regular shift logic remains unchanged
            while ($currentIndex < count($attendances)) {
                $entry = Carbon::parse($attendances[$currentIndex]->datetime);
                if ($entry->format('Y-m-d') == $targetDate) {
                    $entries[] = $attendances[$currentIndex];
                    $currentIndex++;
                } else {
                    break;
                }
            }
        }

        return [
            'entries' => $entries,
            'lastIndex' => $currentIndex
        ];
    }

    private function createAttendanceEntry($checkIn, $checkOut)
    {
        if ($this->isNightShift) {
            $calculationCheckIn = $checkIn ? $checkIn->copy()->addHours(5) : null;
            $calculationCheckOut = $checkOut ? $checkOut->copy()->addHours(5) : null;
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

    private function calculateWorkingMinutes($groupedAttendances,$gazetteHolidays)
    {
        $totalMinutesWorked = 0;
        $totalHolidayMinutesWorked = 0;
        $totalOvertimeMinutes = 0;
        $earlyCheckinMinutes = [];
        $dailyMinutes = [];
        $lateMinutes = [];
        $overMinutes = [];
        $gazatteMinutes = 0;

        foreach ($groupedAttendances as $date => $entries) {
            $shiftTimes = $this->getShiftTimes($date);
            $results = $this->calculateDailyMinutes($entries, $shiftTimes, $date,$gazetteHolidays);
               
            $dailyMinutes[$date] = $results['totalMinutes'];

            $earlyCheckinMinutes[$date] = $results['earlyCheckinMinutes'];
            $totalMinutesWorked += $dailyMinutes[$date];
            $totalHolidayMinutesWorked += $results['holidayMinutes'];
            $totalOvertimeMinutes += $results['overtimeMinutes'];
            $gazatteMinutes += $results['gazatteMinutes'];
            $lateMinutes[$date] = $results['lateMinutes'];
            $overMinutes[$date] = $results['overtimeMinutes'];
            
        }
        return [
            'dailyMinutes' => $dailyMinutes,
            'totalMinutesWorked' => $totalMinutesWorked,
            'totalHolidayMinutesWorked' => $totalHolidayMinutesWorked,
            'totalOvertimeMinutes' => $totalOvertimeMinutes,
            'earlyCheckinMinutes' => $earlyCheckinMinutes,
            'lateMinutes' => $lateMinutes,
            'overMinutes' => $overMinutes,
            'gazatteMinutes' => $gazatteMinutes,
        ];
    }

    private function getShiftTimes($date)
    {
        $shiftStartTime = Carbon::parse($this->shift->start_time)
            // ->addHours($this->isNightShift ? 5 : 0)
            ->format('H:i:s');
        $shiftEndTime = Carbon::parse($this->shift->end_time)
            // ->addHours($this->isNightShift ? 5 : 0)
            ->format('H:i:s');

        $shiftStart = Carbon::parse($date . ' ' . $shiftStartTime);
        $shiftEnd = Carbon::parse($date . ' ' . $shiftEndTime);

        // if ($this->isNightShift) {
        //     $shiftEnd->addDay();
        // }

        return ['start' => $shiftStart, 'end' => $shiftEnd];
    }

    private function calculateDailyMinutes($entries, $shiftTimes, $date,$gazetteHolidays)
    {
        $totalMinutes = 0;
        $holidayMinutes = 0;
        $overtimeMinutes = 0;
        $earlyCheckinMinutes = 0;
        $lateMinutes = 0;
        $gazatteMinutes = 0;

        $gazetteDays =  $gazetteHolidays->pluck('holiday_date')->map(function ($date) {
            return date('Y-m-d', strtotime($date));
        })->toArray();

        foreach ($entries as $key => $entry) {
            if (!$entry['is_incomplete']) {
                $minutes = $this->calculateEntryMinutes($entry, $shiftTimes);
                $totalMinutes += $minutes['worked'];
                // dd($minutes['worked']);
                
                if (in_array(Carbon::parse($date)->format('l'), $this->holidays) || in_array($date, $gazetteDays)) {
                    // var_dump($minutes['worked']);
                    $holidayMinutes += $minutes['worked'];
                }

                if (in_array($date, $gazetteDays) && !in_array(Carbon::parse($date)->format('l'), $this->holidays)) {
                    $gazatteMinutes += $minutes['worked'];
                }

                $overtimeMinutes += $minutes['overtime'];
                $earlyCheckinMinutes += $minutes['earlyCheckin'];
                if($key == 0){
                    $lateMinutes += $minutes['late'];
                }
            }
        }
        
        return [
            'totalMinutes' => $totalMinutes,
            'holidayMinutes' => $holidayMinutes,
            'overtimeMinutes' => $overtimeMinutes,
            'earlyCheckinMinutes' => $earlyCheckinMinutes,
            'lateMinutes' => $lateMinutes,
            'gazatteMinutes' => $gazatteMinutes,
        ];
    }

    private function calculateEntryMinutes($entry, $shiftTimes)
    {
        
        $startTime = $entry['calculation_checkin']->max($shiftTimes['start']);
        $endTime = $entry['calculation_checkout']->min($shiftTimes['end']);
        // dd($startTime,$endTime,$entry,$shiftTimes['start'],$shiftTimes['end']);

        $earlyCheckinMinutes = 0;
        if($this->isNightShift){
            $earlyCheckinMinutes = $entry['original_checkin']->lt($shiftTimes['start']) 
            ? $entry['original_checkin']->diffInMinutes($shiftTimes['start']) 
            : 0;
        } else {
            $earlyCheckinMinutes = $entry['calculation_checkin']->lt($shiftTimes['start']) 
            ? $entry['calculation_checkin']->diffInMinutes($shiftTimes['start']) 
            : 0;
        }
        
        $lateMinutes = 0;
        if($this->isNightShift){
            $lateMinutes = $entry['original_checkin']->gt($shiftTimes['start']) 
            ? abs($entry['original_checkin']->diffInMinutes($shiftTimes['start']) )
            : 0;
        } else {
            $lateMinutes = $entry['calculation_checkin']->gt($shiftTimes['start']) 
            ? abs($entry['calculation_checkin']->diffInMinutes($shiftTimes['start']) )
            : 0;
        }
        
        $overtimeMinutes = 0;
        if($this->isNightShift){
            $overtimeMinutes = $entry['original_checkout']->gt($shiftTimes['end']->copy()->addDay()) 
            ? abs($entry['original_checkout']->diffInMinutes($shiftTimes['end']->copy()->addDay()))
            : 0;
        } else {
            $overtimeMinutes = $entry['calculation_checkout']->gt($shiftTimes['end']) 
            ? abs($entry['calculation_checkout']->diffInMinutes($shiftTimes['end'])) 
            : 0;
        }
        
        
        
        if (!$this->isNightShift) {
            $minutesWorked = $startTime->diffInMinutes($endTime);
            $totalWorkedMinutes = $entry['calculation_checkin']->diffInMinutes($entry['calculation_checkout']);
        
            return [
                'worked' => $minutesWorked,
                'overtime' => $overtimeMinutes,
                'earlyCheckin' => $earlyCheckinMinutes,
                'late' => $lateMinutes
            ];
        } else {
            $minutesWorked = $startTime->diffInMinutes($endTime->copy()->addDay());
            // dd($minutesWorked,$startTime,$endTime->copy()->addDay());
            $totalWorkedMinutes = $entry['calculation_checkin']->diffInMinutes($entry['calculation_checkout']);
        
            return [
                'worked' => $totalWorkedMinutes,
                'overtime' => $overtimeMinutes,
                'earlyCheckin' => $earlyCheckinMinutes,
                'late' => $lateMinutes
            ];
        }
        

        return ['worked' => 0, 'overtime' => 0,'earlyCheckin' => $earlyCheckinMinutes, 'late' => 0];
    }

}

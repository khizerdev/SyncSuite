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
        // dd($this->holidays);
        $this->holidayRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->holiday_ratio ?? 0;
        $this->overTimeRatio = $employee->type->adjustment == 1 ? 0 : $employee->type->overtime_ratio ?? 0;
        $this->isNightShift = Carbon::parse($this->shift->start_time)->greaterThan(Carbon::parse($this->shift->end_time));
        $this->isContract = $employee->type->name == "Contract" ? true : false;
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
            // dd($this->employee);
            return null;
        }
        $gazetteHolidays = $this->getGazatteHolidays($startDate,$endDate);
        $attendances = $this->getAttendances($userInfo->id, $startDate, $endDate);
        // dd($endDate);
        $dates = $this->initializeDates($startDate, $endDate,$gazetteHolidays);
        $groupedAttendances = $dates['groupedAttendances'];
        $workingDays = $dates['workingDays'];
        $monthDays = $dates['monthDays'];
        $holidayDays = $dates['holidayDays'];
        $totalDays = $dates['totalDays'];

        $processedAttendances = $this->processAttendanceRecords($attendances, $groupedAttendances);
        $calculatedMinutes = $this->calculateWorkingMinutes($processedAttendances['groupedAttendances'],$gazetteHolidays);
        
        if ($this->isContract) {
            // Daily minutes
            $calculatedMinutes['dailyMinutes'] = array_map(function($val) {
                return $val > 0 ? 720 : $val;
            }, $calculatedMinutes['dailyMinutes']);
            
            // Early check-in minutes
            $calculatedMinutes['earlyCheckinMinutes'] = array_fill(
                0, 
                count($calculatedMinutes['earlyCheckinMinutes']), 
                0
            );
            
            // Early check-out minutes
            $calculatedMinutes['earlyCheckoutMinutes'] = array_fill(
                0, 
                count($calculatedMinutes['earlyCheckoutMinutes']), 
                0
            );
            
            // Early lateMinutes minutes
            $calculatedMinutes['lateMinutes'] = array_fill(
                0, 
                count($calculatedMinutes['lateMinutes']), 
                0
            );
            
            // Early overMinutes minutes
            $calculatedMinutes['overMinutes'] = array_fill(
                0, 
                count($calculatedMinutes['overMinutes']), 
                0
            );
            
        }
      

        $missScanCount = $this->getMissScanCount($processedAttendances['groupedAttendances']);
        // dd($calculatedMinutes['totalHolidayMinutesWorked']);
        $actualHoursWorked = ($calculatedMinutes['totalMinutesWorked'] / 60) - ($calculatedMinutes['totalHolidayMinutesWorked'] / 60) - ($calculatedMinutes['gazatteMinutes'] /60 );
        
        $gazatteDates = [];
        
        foreach($gazetteHolidays->toArray() as $gazatteDate){
            
            if(!in_array(Carbon::parse($gazatteDate["holiday_date"])->format('l'),$this->holidays)){
                
                array_push($gazatteDates,Carbon::parse($gazatteDate["holiday_date"])->format('Y-m-d'));
            }
        }
        
        // dd($calculatedMinutes);
        foreach ($processedAttendances['groupedAttendances'] as $date => &$entries) {
            // dd($entries);
            $dailyMinutes = 0;
            $dailyMinutesCalculated = false; // Flag to ensure daily minutes are calculated only once

            foreach ($entries as &$entry) {
                if (!$entry['is_incomplete']) {
                    $entryStart = $entry['original_checkin'];
                    $entryEnd = $entry['original_checkout'];
                    
                    if (!$dailyMinutesCalculated) {
                        $dailyMinutes += $calculatedMinutes['dailyMinutes'][$date] - $calculatedMinutes['earlyCheckinMinutes'][$date] - $calculatedMinutes['overMinutes'][$date];
                        $dailyMinutesCalculated = true; // Set the flag to true after calculation
                    }
                }
                // var_dump($dailyMinutes);
            }
            // dd($dailyMinutes);
            // Add dailyMinutes to each entry for the date
            foreach ($entries as &$entry) {
                $entry['dailyMinutes'] = $dailyMinutes;
                $entry['overMinutes'] = $calculatedMinutes['overMinutes'][$date];
                $entry['earlyMinutes'] = $calculatedMinutes['earlyCheckinMinutes'][$date];
                $entry['earlyOutMinutes'] = $calculatedMinutes['earlyCheckoutMinutes'][$date];
            }
        }
        
        $cappedOverMinutes = array_map(
            function($value) {
                return $value > 150 ? 150 : 150;
            },
            $calculatedMinutes['overMinutes']
        );
        
        $cappedLateMinutes = array_map(
            function($value) {
                if($this->shift->id == "12"){
                    if($value > 60){
                        return $value - 60;
                    } else {
                        return $value;
                    }
                } else {
                    return $value;
                }
                
            },
            $calculatedMinutes['lateMinutes']
        );
        
      
        return [
            'employee' => $this->employee,
            
            'shift' => $this->shift,
            'isNightShift' => $this->isNightShift,
        
            'dailyMinutes' => $calculatedMinutes['dailyMinutes'],
            'earlyCheckinMinutes' => $calculatedMinutes['earlyCheckinMinutes'],
            'earlyCheckoutMinutes' => $calculatedMinutes['earlyCheckoutMinutes'],
            'lateMinutes' => $cappedLateMinutes,
            'overMinutes' => $calculatedMinutes['overMinutes'],
            'overMinutesOfAutoShift' => $cappedOverMinutes,
            'totalMinutesWorked' => $calculatedMinutes['totalMinutesWorked'],
            'totalWorkingHours' => $calculatedMinutes['totalMinutesWorked'] / 60, // Insightful
            'totalOvertimeMinutes' => number_format($calculatedMinutes['totalOvertimeMinutes'],2),
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
            
            'workingDays' => $totalDays - $this->getAbsentCount($startDate, $endDate, $processedAttendances['groupedAttendances'], $gazetteHolidays),
            'monthDays' => $monthDays,
            'effectiveWorkingDays' => $workingDays - $holidayDays - count($gazetteHolidays),
            'productivityRatio' => ($calculatedMinutes['totalMinutesWorked'] / 60) / ($workingDays * 8),
            'month' => $startDate instanceof Carbon ? $startDate->format('m') : Carbon::parse($startDate)->format('m'),
            'year' => $startDate instanceof Carbon ? $startDate->format('Y') : Carbon::parse($startDate)->format('Y'),
            
        ];
        
        
    }

    public function getShiftDetails($date)
    {
        // Get the shift for the given date
        $shift = $this->getShiftForDate($date) ? $this->getShiftForDate($date) : $this->shift;
        $isNightShift = Carbon::parse($shift->start_time)->greaterThan(Carbon::parse($shift->end_time));
        return [
            'shift' => $shift,
            'is_night_shift' => $isNightShift,
        ];
    }

    public function getShiftForDate($date)
    {
        $date = Carbon::parse($date);

        // Find the most recent shift transfer for the given date
        $shiftTransfer = $this->employee->shiftTransfers()
            ->where('from_date', '<=', $date)
            ->orderBy('from_date', 'desc')
            ->first();
        
        return $shiftTransfer ? $shiftTransfer->shift : null;
    }

    public function getMissScanCount($groupedAttendances){
// dd($groupedAttendances);
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
    
//   public function getAbsentCount($groupedAttendances)
// {
    
//     $absentCount = 0;

//     if (is_array($groupedAttendances)) {
//         foreach ($groupedAttendances as $values) {
            
//             // Check if this single entry is an empty array
//             if (is_array($values) && empty($values)) {
//                 $absentCount++;
//             }

//             // If values is not an array (unexpected structure), count as absent as well
//             if (!is_array($values)) {
//                 $absentCount++;
//             }
//         }
//     }
    
//     return $absentCount;
// }
  public function getAbsentCount($startDate, $endDate, $groupedAttendances, $gazetteHolidays)
{
    $absentCount = 0;
    
    // Prepare gazette holiday dates
    $gazetteDates = array_map(function($holiday) {
        return Carbon::parse($holiday["holiday_date"])->format('Y-m-d');
    }, $gazetteHolidays->toArray());

    if (!is_array($groupedAttendances)) {
        return 0;
    }

    foreach ($groupedAttendances as $date => $attendanceRecords) {
        $currentDate = Carbon::parse($date);
        
        // Skip if it's a weekend holiday or gazette holiday
        if (in_array($currentDate->format('l'), $this->holidays) || 
            in_array($currentDate->format('Y-m-d'), $gazetteDates)) {
            continue;
        }

        // Count as absent if no attendance records exist for this working day
           
        if (empty($attendanceRecords)) {
            $absentCount++;
        }
    }

    return $absentCount;
}


    private function getUserInfo()
    {
        return UserInfo::where('code', $this->employee->code)->first();
    }

    private function getAttendances($userId, $startDate, $endDate)
    {
        $shiftDetails = $this->getShiftDetails($endDate);
        $isNightShift = $shiftDetails['is_night_shift'];

        if ($isNightShift) {
            $endDate = Carbon::parse($endDate)->copy()->addDays(1)->setTime(12, 0, 0)->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($endDate)->copy()->endOfDay()->format('Y-m-d H:i:s');
        }
        // dd(Attendance::where('code', $userId)
        //     ->whereBetween('datetime', [$startDate, $endDate])
        //     ->orderBy('datetime')
        //     ->get());
        return Attendance::where('code', $userId)
            ->whereBetween('datetime', [$startDate, $endDate])
            ->orderBy('datetime')
            ->get();
    }

    private function initializeDates($startDate, $endDate,$gazetteHolidays)
    {
        $groupedAttendances = [];
        $workingDays = 0;
        $holidayDays = 0;
        $monthDays = 0;
        $totalDays = 0;
        // $currentDate = clone $startDate;
        $currentDate = Carbon::parse($startDate);
        
        $gazatteDates = [];
        foreach($gazetteHolidays->toArray() as $gazatteDate){
            // dd(Carbon::parse($gazatteDate["holiday_date"])->format('Y-m-d'));
            array_push($gazatteDates,Carbon::parse($gazatteDate["holiday_date"])->format('Y-m-d'));
        }
        while ($currentDate->lte($endDate)) {
        $totalDays++;
            $date = $currentDate->format('Y-m-d');
            $groupedAttendances[$date] = [];
            
            // if (in_array($currentDate->format('l'), $this->holidays) || in_array(Carbon::parse($currentDate)->format('Y-m-d'),$gazatteDates)) {
                $workingDays++;
            // }
            if (in_array($currentDate->format('l'), $this->holidays) || in_array(Carbon::parse($currentDate)->format('Y-m-d'),$gazatteDates)) {
                $workingDays++;
            }
            if (in_array($currentDate->format('l'), $this->holidays)) {
                // var_dump($currentDate);
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
            'totalDays' => $totalDays,
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

            $currentDateEntries = $this->getCurrentDateEntries($attendances, $i, $date);
            if (count($currentDateEntries['entries']) > 0) {
                $nestedEntries = $this->determineDailyEntries($currentDateEntries['entries']);
                foreach ($nestedEntries as $entry) {
                    $groupedAttendances[$date][] = $this->createAttendanceEntry($entry['checkIn'], $entry['checkOut'],$entry,$date);
                    
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
        

        // Handle special case for shift ID 2
        // if ($this->shift->id == 12) {
        //     $entryHour = (int)$entryTime->format('H');
            
            
        //     if ($entryHour < 7) {
        //         // dd($entry);
                
        //         if ($currentCheckIn) {
        //             $nestedEntries[] = [
        //                 'checkIn' => $currentCheckIn,
        //                 'checkOut' => $entryTime
        //             ];
        //             $lastCheckOut = $entryTime;
        //             $currentCheckIn = null;
        //         }
                
        //         continue;
        //     }
        // }

        if (!$currentCheckIn) {
            // Ensure this is not immediately after the last checkout
            if ($lastCheckOut && $lastCheckOut->diffInMinutes($entryTime) <= 30) {
                continue;
            }
            $currentCheckIn = $entryTime;
            continue;
        }

        if ($currentCheckIn->diffInMinutes($entryTime) > 30) {
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
    $entries = [];
    $currentIndex = $startIndex;
    $targetDateObj = Carbon::parse($targetDate);
    $shiftDetails = $this->getShiftDetails($targetDate);
    $isNightShift = $shiftDetails['is_night_shift'];

    // Handle special case for shift ID 2
    if ($shiftDetails["shift"]->id == 12) {
        while ($currentIndex < count($attendances)) {
            $entry = Carbon::parse($attendances[$currentIndex]->datetime);
            $entryDate = $entry->format('Y-m-d');
            $entryHour = (int)$entry->format('H');
            // dd($targetDate);
            // Only consider entries from 7 AM to 11:59 PM on the target date
            if ($entryDate === $targetDate && $entryHour >= 7) {
                $entries[] = $attendances[$currentIndex];
                $currentIndex++;
                continue;
            }
        
            $nextDay = $targetDateObj->copy()->addDay()->format('Y-m-d');
               
            if ($entryDate === $nextDay && $entryHour < 7) {
                $entries[] = $attendances[$currentIndex];
                $currentIndex++;
                continue;
            }
            
            // If we reach a different date, we're done
            // if ($entryDate !== $targetDate) {
                break;
            // }
        }
    } 
    elseif ($isNightShift) {
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

    private function createAttendanceEntry($checkIn, $checkOut,$entry, $date)
    {
        $shiftDetails = $this->getShiftDetails($date);
        $isNightShift = $shiftDetails['is_night_shift'];

        if ($isNightShift) {
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
        $earlyCheckoutMinutes = [];
        $dailyMinutes = [];
        $lateMinutes = [];
        $overMinutes = [];
        $gazatteMinutes = 0;

        foreach ($groupedAttendances as $date => $entries) {
            $shiftTimes = $this->getShiftTimes($date);
            $results = $this->calculateDailyMinutes($entries, $shiftTimes, $date,$gazetteHolidays);
            // dd($results);
               
            $dailyMinutes[$date] = $results['totalMinutes'];

            $earlyCheckinMinutes[$date] = $results['earlyCheckinMinutes'];
            $earlyCheckoutMinutes[$date] = $results['earlyCheckoutMinutes'];
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
            'earlyCheckoutMinutes' => $earlyCheckoutMinutes,
            'lateMinutes' => $lateMinutes,
            'overMinutes' => $overMinutes,
            'gazatteMinutes' => $gazatteMinutes,
        ];
    }

    private function getShiftTimes($date)
    {
        $currentShift = $this->getShiftDetails($date);
        $shiftStartTime = Carbon::parse($currentShift['shift']->start_time)
            // ->addHours($this->isNightShift ? 5 : 0)
            ->format('H:i:s');
        $shiftEndTime = Carbon::parse($currentShift['shift']->end_time)
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
        $earlyCheckoutMinutes = 0;
        $lateMinutes = 0;
        $gazatteMinutes = 0;

        $gazetteDays =  $gazetteHolidays->pluck('holiday_date')->map(function ($date) {
            return date('Y-m-d', strtotime($date));
        })->toArray();

        foreach ($entries as $key => $entry) {
            if (!$entry['is_incomplete']) {
                $minutes = $this->calculateEntryMinutes($entry, $shiftTimes, $date);
                $totalMinutes += $minutes['worked'];
                // if($date == "2024-12-07"){
                //     dd($totalMinutes);
                // }
                
                if (in_array(Carbon::parse($date)->format('l'), $this->holidays) || in_array($date, $gazetteDays)) {
                    // var_dump($minutes['worked']);
                    $holidayMinutes += $minutes['worked'];
                }

                if (in_array($date, $gazetteDays) && !in_array(Carbon::parse($date)->format('l'), $this->holidays)) {
                    $gazatteMinutes += $minutes['worked'];
                }

                $overtimeMinutes += $minutes['overtime'];
                $earlyCheckinMinutes += $minutes['earlyCheckin'];
                $earlyCheckoutMinutes += $minutes['earlyCheckout'];
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
            'earlyCheckoutMinutes' => $earlyCheckoutMinutes,
            'lateMinutes' => $lateMinutes,
            'gazatteMinutes' => $gazatteMinutes,
        ];
    }

    private function calculateEntryMinutes($entry, $shiftTimes, $date)
    {
        
        $startTime = $entry['calculation_checkin']->max($shiftTimes['start']);
        $endTime = $entry['calculation_checkout']->min($shiftTimes['end']);
        // dd($startTime,$endTime,$entry,$shiftTimes['start'],$shiftTimes['end']);
        $shiftDetails = $this->getShiftDetails($date);
        $isNightShift = $shiftDetails['is_night_shift'];
        // dd($entry);

        $earlyCheckinMinutes = 0;
        if($isNightShift){
            $earlyCheckinMinutes = $entry['original_checkin']->lt($shiftTimes['start']) 
            ? $entry['original_checkin']->diffInMinutes($shiftTimes['start']) 
            : 0;
        } else {
            $earlyCheckinMinutes = $entry['original_checkin']->lt($shiftTimes['start']) 
            ? $entry['original_checkin']->diffInMinutes($shiftTimes['start']) 
            : 0;
        }
        
        $earlyCheckoutMinutes = 0;
        if($isNightShift){
            $earlyCheckoutMinutes = $entry['original_checkout']->lt($shiftTimes['end']) 
            ? $entry['original_checkout']->diffInMinutes($shiftTimes['end']) 
            : 0;
        } else {
            // dd($entry);
            $earlyCheckoutMinutes = $entry['original_checkout']->lt($shiftTimes['end']) 
            ? $entry['original_checkout']->diffInMinutes($shiftTimes['end']) 
            : 0;
        }
        
        $lateMinutes = 0;
        if($isNightShift){
           
             $lateMinutes = $entry['original_checkin']->gt($shiftTimes['start']) 
            ? abs($entry['original_checkin']->diffInMinutes($shiftTimes['start']) ): 0;
            
        } else {
            
             $lateMinutes = $entry['original_checkin']->gt($shiftTimes['start']) 
            ? abs($entry['original_checkin']->diffInMinutes($shiftTimes['start']) ) : 0;
            // dd($entry['original_checkin'],);
            
        }
        
        $overtimeMinutes = 0;
        if($isNightShift){
            $overtimeMinutes = $entry['original_checkout']->gt($shiftTimes['end']->copy()->addDay()) 
            ? abs($entry['original_checkout']->diffInMinutes($shiftTimes['end']->copy()->addDay()))
            : 0;
        } else {
            $overtimeMinutes = $entry['original_checkout']->gt($shiftTimes['end']) 
            ? abs($entry['original_checkout']->diffInMinutes($shiftTimes['end'])) 
            : 0;
            if($date == "2024-12-07"){
                // dd($entry['original_checkout'], $shiftTimes['start']);
                // dd($entry['original_checkin']);
            }
        }
        
        if (!$isNightShift) {
            $minutesWorked = $startTime->diffInMinutes($endTime);
            // dd($startTime,$endTime);
            // $minutesWorked = $startTime->diffInMinutes($endTime);
            $totalWorkedMinutes = $entry['original_checkin']->diffInMinutes($entry['original_checkout']);
            // if($date == "2025-02-01"){
            //     var_dump($totalWorkedMinutes);
            // }
            return [
                'worked' => $totalWorkedMinutes,
                'overtime' => $overtimeMinutes,
                'earlyCheckin' => $earlyCheckinMinutes,
                'earlyCheckout' => $earlyCheckoutMinutes,
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
                'earlyCheckout' => $earlyCheckoutMinutes,
                'late' => $lateMinutes
            ];
        }
        

        return ['worked' => 0, 'overtime' => 0,'earlyCheckin' => $earlyCheckinMinutes, 'late' => 0];
    }

}
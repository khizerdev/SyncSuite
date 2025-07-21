@props([
    'groupedAttendances',
    'employee',
    'holidays',
    'lateMinutes',
    'earlyMinutes',
    'earlyOutMinutes',
    'overMinutes',
    'gazatteDates',
])

<style>
    #salary-box tr {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

@media print {
    .hide-on-print {
        display:none;
    }
    #salary-box, #attd-table {
        zoom: 0.45 !important; 
    }
    }
}
</style>

<table class="table table-bordered" id="attd-table">
    <thead>
        <tr>
            <th>Date</th>
            <th class="text-center">Entries</th>
            <th>W.H</th>
            <th>L/M</th>
            <th>EI/M</th>
            <th>EO/M</th>
            <th>OT/M</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $lateTimeMinutes = 0;
            $totalMi = 0;
            $dates = array_keys($groupedAttendances);
            $gazatteDatesArray = array_map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            }, $gazatteDates);
        @endphp
        @foreach ($groupedAttendances as $date => $entries)
            @php
                $dailyMinutes = 0;
                $entryCount = count($entries);
                $dayName = \Carbon\Carbon::parse($date)->format('l');
                $dateFormatted = \Carbon\Carbon::parse($date)->format('Y-m-d');
                $dailyMinutesCalculated = false;

                foreach ($entries as $entry) {
                    if (!$entry['is_incomplete']) {
                        $entryStart = $entry['calculation_checkin'];
                        $entryEnd = $entry['calculation_checkout'];

                        if (!$dailyMinutesCalculated) {
                            $dailyMinutes += floor($dailyMinutes) + $entry['dailyMinutes'];
                            $dailyMinutesCalculated = true;
                        }

                        $lateTimeMinutes += $lateMinutes[$date];
                    }
                }
                $totalMi += $dailyMinutes;
                if ($dailyMinutes > 1) {
                    $dailyHours = sprintf('%02d:%02d', floor($dailyMinutes) / 60, $dailyMinutes % 60);
                } else {
                    $dailyHours = sprintf('%02d:%02d', floor(0) / 60, 0 % 60);
                }

                // Check for sandwich leave condition (only for holidays)
                $isSandwichHoliday = false;
if (in_array($dayName, $holidays) || in_array($dateFormatted, $gazatteDatesArray)) {
    // Function to find the nearest non-holiday previous day
    $findPreviousNonHoliday = function($date) use ($holidays, $gazatteDatesArray) {
        $prevDay = \Carbon\Carbon::parse($date)->subDay();
        while (true) {
            $prevDayFormatted = $prevDay->format('Y-m-d');
            $prevDayName = $prevDay->format('l');
            
            if (!in_array($prevDayName, $holidays) && !in_array($prevDayFormatted, $gazatteDatesArray)) {
                return $prevDayFormatted;
            }
            $prevDay->subDay();
        }
    };

    // Function to find the nearest non-holiday next day
    $findNextNonHoliday = function($date) use ($holidays, $gazatteDatesArray) {
        $nextDay = \Carbon\Carbon::parse($date)->addDay();
        while (true) {
            $nextDayFormatted = $nextDay->format('Y-m-d');
            $nextDayName = $nextDay->format('l');
            
            if (!in_array($nextDayName, $holidays) && !in_array($nextDayFormatted, $gazatteDatesArray)) {
                return $nextDayFormatted;
            }
            $nextDay->addDay();
        }
    };

    // Get the nearest non-holiday previous day
    $prevNonHoliday = $findPreviousNonHoliday($date);
    $prevDayIsAbsent =
        isset($groupedAttendances[$prevNonHoliday]) &&
        count($groupedAttendances[$prevNonHoliday]) < 1;

    // Get the nearest non-holiday next day
    $nextNonHoliday = $findNextNonHoliday($date);
    $nextDayIsAbsent =
        isset($groupedAttendances[$nextNonHoliday]) &&
        count($groupedAttendances[$nextNonHoliday]) < 1;

    $isSandwichHoliday = $prevDayIsAbsent && $nextDayIsAbsent;
}
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('l, M j, y') }}</td>
                <td>
                    <div class="entries-container">
                        @foreach ($entries as $entry)
                            <div class="entry-card mb-2 @if (!$loop->last) border-bottom pb-2 @endif">
                                <div class="d-flex justify-content-between align-items-center entry-stat">
                                    <div class="entry-time">
                                        @if ($entry['original_checkin'])
                                            In: {{ $entry['original_checkin']->format('h:i A') }}
                                        @endif
                                    </div>
                                    <div class="entry-time">
                                        @if ($entry['original_checkout'])
                                            Out: {{ $entry['original_checkout']->format('h:i A') }}
                                        @else
                                            <span class="text-danger"> No Checkout</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td>
                    {{ $dailyHours }}
                </td>
                <td>{{ round($lateMinutes[$date]) ?? 0 }}</td>
                <td>{{ round($earlyMinutes[$date]) ?? 0 }}</td>
                <td>{{ round($earlyOutMinutes[$date]) ?? 0 }}</td>
                <td>{{ round($overMinutes[$date]) ?? 0 }}</td>
                @php
                    $hasIncomplete = collect($entries)->contains('is_incomplete', true);
                @endphp
                <td>
                    @if (in_array($dayName, $holidays) || in_array($dateFormatted, $gazatteDatesArray))
                        @if ($isSandwichHoliday)
                            <span class="text-danger">Sandwich Leave</span>
                        @else
                            <span class="text-danger">Holiday</span>
                        @endif
                    @elseif(empty($entries))
                        <span class="text-danger">Absent</span>
                    @elseif($dailyMinutes > 0 && !$hasIncomplete)
                        <span class="text-success">Present</span>
                    @else
                        <span class="text-danger">Misscan</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<style>
    .entries-container {
        max-height: 150px;
        overflow-y: auto;
        padding-right: 5px
    }

    /* .entry-card {
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 8px;
    }

    .entry-card:hover {
        background-color: #e9ecef;
    } */

    .entry-time {
        display: inline-block;
    }


    /* Custom scrollbar for webkit browsers */
    .entries-container::-webkit-scrollbar {
        width: 3px;
    }

    .entries-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .entries-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .entries-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

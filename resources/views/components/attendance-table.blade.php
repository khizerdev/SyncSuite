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



<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th class="text-center">Entries</th>
            <th>Working Hours</th>
            <th>Late Min</th>
            <th>Early Min</th>
            <th>Early Out</th>
            <th>Over Min</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $lateTimeMinutes = 0;
            $totalMi = 0;
        @endphp
        @foreach ($groupedAttendances as $date => $entries)
            @php
                $dailyMinutes = 0;
                $entryCount = count($entries);
                $dayName = \Carbon\Carbon::parse($date)->format('l');
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

            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('l, M j, Y') }}</td>
                <td>
                    <div class="entries-container">
                        @foreach ($entries as $entry)
                            <div class="entry-card mb-2 @if (!$loop->last) border-bottom pb-2 @endif">
                                <div class="d-flex justify-content-between align-items-center">
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

                    @if (in_array($dayName, $holidays) ||
                            in_array(\Carbon\Carbon::parse($date)->format('Y-m-d'), array_values($gazatteDates)))
                        <span class="text-danger">Holiday</span>
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

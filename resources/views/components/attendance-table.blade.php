@props(['groupedAttendances', 'employee', 'holidays', 'lateMinutes', 'earlyMinutes', 'overMinutes'])



<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Total Working Hours</th>
            <th>Late Minutes</th>
            <th>Early Minutes</th>
            <th>Over Minutes</th>
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

                foreach ($entries as $entry) {
                    if (!$entry['is_incomplete']) {
                        $entryStart = $entry['calculation_checkin'];
                        $entryEnd = $entry['calculation_checkout'];
                        $dailyMinutes +=
                            $dailyMinutes +
                            $entryStart->diffInMinutes($entryEnd) -
                            floor($earlyMinutes[$date]) -
                            floor($overMinutes[$date]);
                        $lateTimeMinutes += floor($lateMinutes[$date]);
                    }
                }
                $totalMi += $dailyMinutes;
                $dailyHours = sprintf('%02d:%02d', $dailyMinutes / 60, $dailyMinutes % 60);
            @endphp
            <tr>
                <td>{{ Carbon\Carbon::parse($date)->format('l, F j, Y') }}</td>
                <td>
                    @if (isset($entries[0]['original_checkin']))
                        {{ $entries[0]['original_checkin']->format('h:i A') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if (isset($entries[0]['original_checkout']))
                        {{ $entries[0]['original_checkout']->format('h:i A') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $dailyHours }}</td>
                <td>{{ floor($lateMinutes[$date]) }}</td>
                <td>{{ floor($earlyMinutes[$date]) }}</td>
                <td>{{ floor($overMinutes[$date]) }}</td>
                <td>
                    @if (in_array($dayName, $holidays))
                        <span class="text-danger">Holiday</span>
                    @elseif(empty($entries))
                        <span class="text-danger">Absent</span>
                    @elseif(!empty($entries) && !$entries[0]['is_incomplete'])
                        <span class="text-success">Present</span>
                    @elseif(!empty($entries) && $entries[0]['is_incomplete'])
                        <span class="text-danger">Misscan</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@props(['groupedAttendances', 'employee', 'holidays'])



<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Total Working Hours</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedAttendances as $date => $entries)
            @php
                $dailyMinutes = 0;
                $entryCount = count($entries);
                $dayName = \Carbon\Carbon::parse($date)->format('l');

                foreach ($entries as $entry) {
                    if (!$entry['is_incomplete']) {
                        $entryStart = $entry['calculation_checkin'];
                        $entryEnd = $entry['calculation_checkout'];
                        $dailyMinutes += $entryStart->diffInMinutes($entryEnd);
                    }
                }

                $dailyHours = sprintf('%02d:%02d', floor($dailyMinutes / 60), $dailyMinutes % 60);
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

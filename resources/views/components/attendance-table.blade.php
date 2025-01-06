@props(['groupedAttendances', 'employee', 'holidays', 'lateMinutes', 'earlyMinutes', 'overMinutes'])



<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Entries</th>
            <th>Total Working Hours</th>
            <th>Late Minutes</th>
            <th>Early Minutes</th>
            <th>Over Minutes</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalWorkingMinutes = 0;
        @endphp
        @foreach ($groupedAttendances as $date => $entries)
            @php
                $dailyMinutes = 0;
                $dayName = \Carbon\Carbon::parse($date)->format('l');
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</td>
                <td>
                    <ul>
                        @foreach ($entries as $entry)
                            <li>
                                @if ($entry['original_checkin'])
                                    In: {{ $entry['original_checkin']->format('h:i A') }}
                                @endif
                                <br />
                                @if ($entry['original_checkout'])
                                    Out: {{ $entry['original_checkout']->format('h:i A') }}
                                @else
                                    <span class="text-danger">No Checkout</span>
                                @endif
                                <br />
                            </li>
                            @php
                                if (!$entry['is_incomplete']) {
                                    $dailyMinutes += $entry['calculation_checkin']->diffInMinutes(
                                        $entry['calculation_checkout'],
                                    );
                                }
                            @endphp
                        @endforeach
                    </ul>
                </td>
                <td>
                    @php
                        $dailyHours = sprintf('%02d:%02d', $dailyMinutes / 60, $dailyMinutes % 60);
                        $totalWorkingMinutes += $dailyMinutes;
                    @endphp
                    {{ $dailyHours }}
                </td>
                <td>{{ floor($lateMinutes[$date]) ?? 0 }}</td>
                <td>{{ floor($earlyMinutes[$date]) ?? 0 }}</td>
                <td>{{ floor($overMinutes[$date]) ?? 0 }}</td>
                <td>
                    @if (in_array($dayName, $holidays))
                        <span class="text-danger">Holiday</span>
                    @elseif(empty($entries))
                        <span class="text-danger">Absent</span>
                    @elseif($dailyMinutes > 0)
                        <span class="text-success">Present</span>
                    @else
                        <span class="text-danger">Misscan</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    {{-- <tfoot>
        <tr>
            <th colspan="2">Total Working Hours</th>
            <th colspan="5">
                {{ sprintf('%02d:%02d', $totalWorkingMinutes / 60, $totalWorkingMinutes % 60) }}
            </th>
        </tr>
    </tfoot> --}}
</table>

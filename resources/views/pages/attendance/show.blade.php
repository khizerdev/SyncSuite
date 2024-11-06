@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            @php
                $isNightShift =
                Carbon\Carbon::parse($shift->start_time)->greaterThan(Carbon\Carbon::parse($shift->end_time));
                $weekends = explode("," , $employee->type->holidays);
                @endphp

                    <!-- Combined Attendance and Salary Table -->
                    <div class="card mb-4">
                        <div class="card-body">
                        <div class="col-12">
                        <h2 class="mb-4">Attendance for Employee: {{ $employee->name }} {{$employee->code}}</h2>
                    </div>
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
                                    @php
                                    $totalHoursWorked = 0;
                                    $totalHolidayHoursWorked = 0;
                                    $holidays = explode(',', $employee->holidays);
                                    $workingDays = Carbon\Carbon::parse('2024-07-01')->daysInMonth;

                                    foreach ($groupedAttendances as $date => $entries) {
                                    $dailyMinutes = 0;
                                    $isHoliday = in_array(Carbon\Carbon::parse($date)->format('l'), $holidays);
                                    $entryCount = count($entries);

                                    foreach ($entries as $entry) {
                                    if (!$entry['is_incomplete']) {
                                    $entryStart = $entry['calculation_checkin'];
                                    $entryEnd = $entry['calculation_checkout'];

                                    $dailyMinutes += $entryStart->diffInMinutes($entryEnd);
                                    }
                                    }

                                    $totalHoursWorked += $dailyMinutes / 60;
                                    $holidayHoursWorked = $isHoliday ? ($dailyMinutes / 60) * $holidayRatio : 0;
                                    $totalHolidayHoursWorked += $holidayHoursWorked;

                                    $status = $entryCount ? ($entryCount == 1 ? 'Incomplete' : 'Complete') : 'Absent';

                                    $dailyHours = sprintf('%02d:%02d', floor($dailyMinutes / 60), $dailyMinutes % 60);
                                    @endphp
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($date)->format('l, F j, Y') }}</td>
                                        <td>
                                            @if(isset($entries[0]['original_checkin']))
                                            {{ $entries[0]['original_checkin']->format('Y-m-d H:i:s') }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($entries[0]['original_checkout']))
                                            {{ $entries[0]['original_checkout']->format('Y-m-d H:i:s') }}
                                            @else
                                            N/A
                                            @endif
                                        </td>


                                        <td>{{ $dailyHours }}</td>
                                        <td>
                                            @if(empty($entries))
                                            <span class="text-danger">Absent</span>
                                            @elseif(empty($entries) && !$entries[0]['is_incomplete'])
                                            <span class="text-success">Present</span>
                                            @elseif(!empty($entries) && !$entries[0]['is_incomplete'])
                                            <span class="text-success">Present</span>
                                            @elseif(empty($entries) && !$entries[0]['is_incomplete'])
                                            <span class="text-danger">Absent</span>
                                            @elseif(!empty($entries) && $entries[0]['is_incomplete'])
                                            <span class="text-danger">Misscan</span>
                                            @endif
                                        </td>

                                    </tr>
                                    @php
                                    }

                                    @endphp

                                </tbody>
                            </table>
                        </div>
                    </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@section('script')

@endsection
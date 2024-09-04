@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center">
                  <div class="col-6">
                    <h2 class="mb-4">Daily Attendance for Employee: {{ $employee->name }}</h2>
                    <p>Shift: {{ $shift->name }} ({{ $shift->start_time->format('H:i') }} to {{ $shift->end_time->format('H:i') }})</p>
                  </div>
                  <div class="col-6 text-right">
                  </div>
              </div>

              </div>

              @php
              $isNightShift = Carbon\Carbon::parse($shift->start_time)->greaterThan(Carbon\Carbon::parse($shift->end_time));
          @endphp
          
          @foreach($groupedAttendances as $date => $entries)
    <div class="card mb-4">
        <div class="card-header">
            <h3>{{ Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h3>
            <h4>Total Time Within Shift: 
                @php
                    $hours = floor($dailyMinutes[$date] / 60);
                    $minutes = $dailyMinutes[$date] % 60;
                    echo sprintf('%02d:%02d', $hours, $minutes);
                @endphp
            </h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Actual Check-In</th>
                        <th>Actual Check-Out</th>
                        <th>Calculation Check-In</th>
                        <th>Calculation Check-Out</th>
                        <th>Considered Check-In</th>
                        <th>Considered Check-Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry['original_checkin']->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($entry['original_checkout'])
                                {{ $entry['original_checkout']->format('Y-m-d H:i:s') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $entry['calculation_checkin']->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($entry['calculation_checkout'])
                                {{ $entry['calculation_checkout']->format('Y-m-d H:i:s') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @php
                                $shiftStartTime = Carbon\Carbon::parse($shift->start_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');
                                $shiftEndTime = Carbon\Carbon::parse($shift->end_time)->addHours($isNightShift ? 6 : 0)->format('H:i:s');
                                
                                $shiftStart = Carbon\Carbon::parse($date . ' ' . $shiftStartTime);
                                $shiftEnd = Carbon\Carbon::parse($date . ' ' . $shiftEndTime);

                                if ($isNightShift) {
                                    $shiftEnd->addDay();
                                }

                                $consideredCheckIn = $entry['calculation_checkin']->copy();

                                if ($entry['calculation_checkin']->lt($shiftStart)) {
                                    $consideredCheckIn = $shiftStart;
                                } elseif ($entry['calculation_checkin']->gt($shiftEnd)) {
                                    $consideredCheckIn = $shiftEnd;
                                }

                                // Convert back to original time for display
                                if ($isNightShift) {
                                    $consideredCheckIn->subHours(6);
                                }

                                echo $consideredCheckIn->format('Y-m-d H:i:s');
                            @endphp
                        </td>
                        <td>
                            @if(!$entry['is_incomplete'])
                                @php
                                    $consideredCheckOut = $entry['calculation_checkout']->copy();

                                    if ($entry['calculation_checkout']->lt($shiftStart)) {
                                        $consideredCheckOut = $shiftStart;
                                    } elseif ($entry['calculation_checkout']->gt($shiftEnd)) {
                                        $consideredCheckOut = $shiftEnd;
                                    }

                                    // Convert back to original time for display
                                    if ($isNightShift) {
                                        $consideredCheckOut->subHours(6);
                                    }

                                    echo $consideredCheckOut->format('Y-m-d H:i:s');
                                @endphp
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($entry['is_incomplete'])
                                <span class="text-danger">Incomplete (No Check-Out)</span>
                            @else
                                <span class="text-success">Complete</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

        </div>
      </div>

    </div>
  </section>

@endsection
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

              @foreach($attendances as $date => $entries)
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
                        <th>Actual Time</th>
                        <th>Considered Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($entry->datetime)->format('H:i:s') }}</td>
                        <td>
                            @php
                                $entryTime = Carbon\Carbon::parse($entry->datetime);
                                $shiftStart = Carbon\Carbon::parse($date)->setTimeFrom($shift->start_time);
                                $shiftEnd = Carbon\Carbon::parse($date)->setTimeFrom($shift->end_time);
                                $consideredTime = $entryTime->copy();

                                if ($entryTime->lt($shiftStart)) {
                                    $consideredTime = $shiftStart;
                                } elseif ($entryTime->gt($shiftEnd)) {
                                    $consideredTime = $shiftEnd;
                                }
                            @endphp
                            {{ $consideredTime->format('H:i:s') }}
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

@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center">
                  <div class="col-6">
                      <h3 class="card-title">Daily Attendance for Employee: {{$employee->name}}</h3>
                  </div>
                  <div class="col-6 text-right">
                  </div>
              </div>

              </div>

              @foreach($attendances as $date => $entries)
    <div class="card mb-4">
        <div class="card-header">
            <h3>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h3>
            <h4>Total Hours: {{ $dailyHours[$date] ?? 0 }}</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $index => $entry)
                    <tr>
                        <td>{{ $index % 2 == 0 ? 'Check In' : 'Check Out' }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry->datetime)->format('H:i:s') }}</td>
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

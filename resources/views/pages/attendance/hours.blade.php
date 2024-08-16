@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Working Hours Summary</h2>
    <p>Total Working Hours: {{ $totalWorkingHours }}</p>
    <p>Required Hours: {{ $requiredHours }}</p>
    <p>Overtime: {{ $overtime }}</p>
    <p>Undertime: {{ $undertime }}</p>

    {{-- <h3>Daily Breakdown:</h3> --}}
    {{-- <ul>
    @foreach($dailyHours as $date => $hours)
        <li>{{ $date }}: {{ $hours }} hours</li>
    @endforeach
    </ul> --}}
</div>
@endsection
@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                    <div class="row">
                    <div class="col-md-6">
                        <h3>Salary Details for August 2024</h3>
                        <p>Employee Holidays: {{ implode(', ', $holidays) }}</p>
                        <p>Total Working Days: {{ $workingDays }} days</p>
                        <p>Total Expected Working Hours: {{ $totalExpectedWorkingDays }} hours</p>
                        <p>No of hours worked: {{ number_format($totalHoursWorked, 2) }} hours</p>
                        <p>Total Holiday Hours Worked: {{ number_format($totalHolidayHoursWorked, 2) }} hours</p>
                        <p>Salary Per Hour: PKR {{ number_format($salaryPerHour, 2) }}</p>
                        <p>Holiday Pay Ratio: {{ $holidayRatio }}x</p>
                        <p>Overtime Pay Ratio: {{ $overTimeRatio }}x</p>
                        <p>Total Overtime Hours Worked: {{ $totalOverTimeHoursWorked }} hours</p>
                        <p>Total Overtime Pay: PKR {{ $totalOvertimePay }}</p>
                        
                        <p>Actual Salary Earned: PKR {{ number_format($actualSalaryEarned - $salary->advance_deducted, 2) }}</p>
                    </div>
                    <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-primary d-print-none"   
                    onclick="window.print();">Print</button>

                        
                    </div>
                    </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header row align-items-center">
                        <div class="col-6">
                            <h2 class="mb-4">Daily Attendance for Employee: {{ $employee->name }}</h2>
                            <p>Shift: {{ $shift->name }} ({{ $shift->start_time->format('H:i') }} to {{
                                $shift->end_time->format('H:i') }})</p>
                        </div>
                    </div>

                </div>

                @php
                $isNightShift =
                Carbon\Carbon::parse($shift->start_time)->greaterThan(Carbon\Carbon::parse($shift->end_time));
                $weekends = explode("," , $employee->type->holidays);
                @endphp

                    <!-- Combined Attendance and Salary Table -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>Attendance Details for August 2024</h3>
                        </div>
                        <div class="card-body">
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
                                    $salaryPerHour = $employee->salary / (12 * 6); // Assuming 12 hours per day, 6 days a week
                                    $holidayRatio = $employee->holiday_ratio;
                                    $holidays = explode(',', $employee->holidays);
                                    $workingDays = Carbon\Carbon::parse('2024-07-01')->daysInMonth;

                                    foreach ($groupedAttendances as $date => $entries) {
                                    $dailyMinutes = 0;
                                    $holidayPay = 0;
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

                                    $actualSalaryEarned = ($dailyMinutes / 60 * $salaryPerHour) + $holidayHoursWorked *
                                    $salaryPerHour;
                                    $status = $entryCount ? ($entryCount == 1 ? 'Incomplete' : 'Complete') : 'Absent';

                                    $dailyHours = sprintf('%02d:%02d', floor($dailyMinutes / 60), $dailyMinutes % 60);
                                    $holidayPay = number_format($holidayHoursWorked * $salaryPerHour, 2);
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

                                    $actualSalaryEarned = ($totalHoursWorked * $salaryPerHour) +
                                    $totalHolidayHoursWorked * $salaryPerHour;
                                    @endphp

                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>


        </div>
    </div>

    </div>
</section>

@endsection
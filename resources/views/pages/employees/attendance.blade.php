@extends('layouts.app')


@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row">
                                @php
                                    $months = [
                                        'January',
                                        'February',
                                        'March',
                                        'April',
                                        'May',
                                        'June',
                                        'July',
                                        'August',
                                        'September',
                                        'October',
                                        'November',
                                        'December',
                                    ];
                                    $month = $months[$salary->month - 1];
                                @endphp

                                <x-salary-details :month="$month" :holidays="$holidays" :working-days="$workingDays" :total-expected-working-days="$totalExpectedWorkingDays"
                                    :total-hours-worked="$totalHoursWorked" :total-holiday-hours-worked="$totalHolidayHoursWorked" :salary-per-hour="$salaryPerHour" :holiday-ratio="$holidayRatio"
                                    :over-time-ratio="$overTimeRatio" :total-over-time-hours-worked="$totalOverTimeHoursWorked" :total-overtime-pay="$totalOvertimePay" :actual-salary-earned="$actualSalaryEarned"
                                    :salary="$salary" />

                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary d-print-none"
                                        onclick="window.print();">Print</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Attendance Details for {{ $month }} 2024</h3>
                        </div>
                        <div class="card-body">
                            <x-attendance-table :grouped-attendances="$groupedAttendances" :employee="$employee" :holidays="$holidays" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

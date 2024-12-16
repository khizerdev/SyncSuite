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

                                <x-salary-details :month="$month" :holidays="$result['holidays']" :working-days="$result['workingDays']" :total-expected-working-days="$salary->expected_hours"
                                    :total-hours-worked="$salary->normal_hours" :total-holiday-hours-worked="$salary->holiday_hours" :salary-per-hour="$result['salaryPerHour']" :holiday-ratio="$salary->holiday_pay_ratio"
                                    :over-time-ratio="$salary->overtime_pay_ratio" :total-over-time-hours-worked="$salary->overtime_hours" :total-overtime-pay="$result['totalOvertimePay']" :actual-salary-earned="$result['actualSalaryEarned']"
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
                            <x-attendance-table :grouped-attendances="$result['groupedAttendances']" :employee="$result['employee']" :holidays="$result['holidays']" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@extends('layouts.app')


@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        @if(isset($results))
                        
                        @foreach($results as $item)
                        
                        @php
                        $salary = $item['salary'];
                        
                        $attendance = $item['attendance'];
                        $result = $item['salary_data'];
                        @endphp
                        
                        <div class="card-header p-0">
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

                                    $overMinutes = array_map(function ($minute) {
                                        return is_numeric($minute) ? floor($minute) : 0;
                                    }, $attendance['overMinutes']);

                                @endphp
                                <x-salary-details :employee="$result['employee']" :salary-month="$month" :early-cut-amount="$result['earlyOutCutAmount']" :early-minutes="$attendance['earlyCheckinMinutes']" :early-out-minutes="$attendance['earlyCheckoutMinutes']"
                                    :month="$month" :holidays="$result['holidays']" :working-days="$result['workingDays']" :worked-days="$result['totalWorkedDays']"
                                    :total-expected-working-days="$salary->expected_hours" :total-hours-worked="$result['totalHoursWorked']" :total-holiday-hours-worked="$result['holidayHours']" :salary-per-hour="$result['salaryPerHour']"
                                    :paid-holiday-amount="$result['normalHolidayPay']" :gazatte-pay-amount="$result['gazattePay']" :holiday-pay-amount="$result['holidayPay']" :holiday-ratio="$salary->holiday_pay_ratio"
                                    :over-time-ratio="$salary->overtime_pay_ratio" :total-over-time-hours-worked="$salary->overtime_hours" :total-over-time-minutes-worked="$result['totalOvertimeMinutes']" :total-overtime-pay="$result['totalOvertimePay']"
                                    :late-minutes="array_sum($attendance['lateMinutes'])" :actual-salary-earned="$result['actualSalaryEarned']" :salary="$salary" :miss-deduct-days="$result['missDeductDays']"
                                    :miss-amount="$result['missAmount']" :holidayOverMins="$result['holidayOverMins']" :sand-wich="$result['sandwichDeduct']" :over-minutes-auto="$result['overMinutesOfAutoShift']" />
                                
                                @if($loop->first)
                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-primary d-print-none"
                                        onclick="window.print();">Print</button>
                                </div>
                                @endif
                            </div>
                        </div>
                        

                        <div class="card-body p-0">
                            <x-attendance-table :grouped-attendances="$attendance['groupedAttendances']" :employee="$attendance['employee']" :holidays="$attendance['holidays']" :late-minutes="$attendance['lateMinutes']"
                                :early-minutes="$attendance['earlyCheckinMinutes']" :early-out-minutes="$attendance['earlyCheckoutMinutes']" :over-minutes="$attendance['overMinutes']" :gazatte-dates="$attendance['gazatteDates']" />
                        </div>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        @endforeach
                        @else
                        
                        <div class="card-header p-0">
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

                                    $overMinutes = array_map(function ($minute) {
                                        return is_numeric($minute) ? floor($minute) : 0;
                                    }, $attendance['overMinutes']);

                                @endphp
                                <x-salary-details :employee="$result['employee']" :salary-month="$month" :early-cut-amount="$result['earlyOutCutAmount']" :early-minutes="$attendance['earlyCheckinMinutes']" :early-out-minutes="$attendance['earlyCheckoutMinutes']"
                                    :month="$month" :holidays="$result['holidays']" :working-days="$result['workingDays']" :worked-days="$result['totalWorkedDays']"
                                    :total-expected-working-days="$salary->expected_hours" :total-hours-worked="$result['totalHoursWorked']" :total-holiday-hours-worked="$result['holidayHours']" :salary-per-hour="$result['salaryPerHour']"
                                    :paid-holiday-amount="$result['normalHolidayPay']" :gazatte-pay-amount="$result['gazattePay']" :holiday-pay-amount="$result['holidayPay']" :holiday-ratio="$salary->holiday_pay_ratio"
                                    :over-time-ratio="$salary->overtime_pay_ratio" :total-over-time-hours-worked="$salary->overtime_hours" :total-over-time-minutes-worked="$result['totalOvertimeMinutes']" :total-overtime-pay="$result['totalOvertimePay']"
                                    :late-minutes="array_sum($attendance['lateMinutes'])" :actual-salary-earned="$result['actualSalaryEarned']" :salary="$salary" :miss-deduct-days="$result['missDeductDays']"
                                    :miss-amount="$result['missAmount']" :holidayOverMins="$result['holidayOverMins']" :sand-wich="$result['sandwichDeduct']" :over-minutes-auto="$result['overMinutesOfAutoShift']" />

                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-primary d-print-none"
                                        onclick="window.print();">Print</button>
                                </div>
                            </div>
                        </div>
                        

                        <div class="card-body p-0">
                            <x-attendance-table :grouped-attendances="$attendance['groupedAttendances']" :employee="$attendance['employee']" :holidays="$attendance['holidays']" :late-minutes="$attendance['lateMinutes']"
                                :early-minutes="$attendance['earlyCheckinMinutes']" :early-out-minutes="$attendance['earlyCheckoutMinutes']" :over-minutes="$attendance['overMinutes']" :gazatte-dates="$attendance['gazatteDates']" />
                        </div>
                        
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </section>
@endsection

@props([
    'month',
    'salaryMonth',
    'employee',
    'holidays',
    'workingDays',
    'workedDays',
    'totalExpectedWorkingDays',
    'totalHoursWorked',
    'totalHolidayHoursWorked',
    'salaryPerHour',
    'holidayRatio',
    'overTimeRatio',
    'totalOverTimeHoursWorked',
    'totalOverTimeMinutesWorked',
    'totalOvertimePay',
    'lateMinutes',
    'actualSalaryEarned',
    'paidHolidayAmount',
    'salary',
    'gazattePayAmount',
    'holidayPayAmount',
    'missDeductDays',
    'missAmount',
    'sandWich',
    'holidayOverMins',
    'earlyMinutes',
    'earlyOutMinutes',
    'earlyCutAmount',
    'overMinutesAuto',
])



<div class="col-md-11">
    <table class="table table-bordered" style="zoom:0.9" id="salary-box">
        <tbody>
            <tr>
                <td colspan="4" class="bg-light"><strong>Salary Details of {{$salaryMonth}} 2025 for Employee:</strong> {{ $employee->name }}
                </td>
            </tr>
            <tr>

            </tr>
            <tr>

                <td><strong>Total WD</strong><br>{{ $workingDays }} days</td>
                <td><strong>Expected WH</strong><br>{{ $totalExpectedWorkingDays }} hours
                </td>
                <td><strong>Hours Worked</strong><br>{{ $totalHoursWorked }} hours
                </td>
                <td><strong>Salary Per Hour</strong><br>PKR {{ number_format($salaryPerHour, 2) }}</td>

                <td class="hide-on-print"><strong>Employee Holiday</strong> <br>{{ implode(', ', $holidays) }}
                </td>
                <td><strong>Holiday Hours</strong><br>{{ $totalHolidayHoursWorked }}
                    hours</td>
                <td class="hide-on-print"><strong>Holiday Pay Ratio</strong><br>{{ $holidayRatio }}x</td>
                <td><strong>Holiday Pay</strong><br>PKR {{ $holidayPayAmount }}</td>
                <td class="hide-on-print"><strong>Overtime Pay Ratio</strong><br>{{ $overTimeRatio }}x</td>
                <td><strong>OT Minutes</strong><br>{{ $totalOverTimeMinutesWorked }} mins</td>
                <td><strong>Overtime Pay</strong><br>PKR {{ $totalOvertimePay }}</td>
                <td><strong>Gazatte Pay</strong><br>PKR {{ $gazattePayAmount }}</td>
                <td><strong>Paid Holiday</strong><br>PKR {{ number_format($paidHolidayAmount, 0) }}
                </td>
                <td><strong>Late Minutes</strong><br>{{ number_format($lateMinutes, 2) }} mins</td>

                <td class="hide-on-print"><strong>Late Cut</strong><br>PKR {{ number_format(($lateMinutes / 60) * $salaryPerHour, 0) }}</td>
                {{-- <td><strong>Initial Salary</strong><br>PKR
                    {{ number_format($actualSalaryEarned, 0) }}</td> --}}
                <td><strong>ADV Deduction</strong><br>PKR
                    {{ number_format($salary->advance_deducted, 0) }}</td>
                <td class="hide-on-print"><strong>Miss Scan Days Counted</strong><br>
                    {{ $missDeductDays }}</td>
                <td class="hide-on-print"><strong>Miss Scan Deduct Amount</strong><br>PKR
                    {{ number_format($missAmount, 0) }}</td>
                <td><strong>Loan Deduction</strong><br>PKR
                    {{ number_format($salary->loan_deducted, 0) }}</td>
                <td><strong>Sandwich Leave</strong><br>
                    {{ $sandWich }}</td>

                </td>
                <td class="hide-on-print"><strong>Total Worked Days</strong><br>{{ $workedDays }} days</td>
                <td class="hide-on-print"><strong>Holiday Over Mins</strong><br>{{ $holidayOverMins }} mins</td>
                <td class="hide-on-print"><strong>Early In Mins</strong><br>{{ number_format(array_sum($earlyMinutes), 2) }} mins</td>
                <td><strong>Early Out Mins</strong><br>{{ number_format(array_sum($earlyOutMinutes), 2) }} mins</td>
                <td class="hide-on-print"><strong>Early Cut Amount</strong><br>PKR {{ number_format($earlyCutAmount, 2) }}</td>
                <!--<td class="hide-on-print"><strong>Over Minutes Auto</strong><br>{{ number_format(array_sum($overMinutesAuto), 2) }} mins</td>-->
                <td class="table-success"><strong>Final Salary</strong><br>PKR
                    {{ number_format(max($actualSalaryEarned - $salary->advance_deducted - $salary->loan_deducted, 0), 2) }}
                </td>
            </tr>
        
        </tbody>
    </table>
</div>

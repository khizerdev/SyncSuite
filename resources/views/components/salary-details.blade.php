@props([
    'month',
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
])

<div class="col-md-11">
    <table class="table table-bordered" style="zoom:0.9">
        <tbody>
            <tr>
                <td colspan="4" class="bg-light"><strong>Employee Name:</strong> {{ $employee->name }}
                </td>
            </tr>
            <tr>

            </tr>
            <tr>

                <td><strong>Total Working Days</strong><br>{{ $workingDays }} days</td>
                <td><strong>Expected Working Hours</strong><br>{{ $totalExpectedWorkingDays }} hours
                </td>
                <td><strong>Hours Worked</strong><br>{{ $totalHoursWorked }} hours
                </td>
                <td><strong>Salary Per Hour</strong><br>PKR {{ number_format($salaryPerHour, 2) }}</td>

            </tr>
            <tr>
                <td><strong>Employee Holidays</strong> <br>{{ implode(', ', $holidays) }}
                </td>
                <td><strong>Holiday Hours</strong><br>{{ $totalHolidayHoursWorked }}
                    hours</td>
                <td><strong>Holiday Pay Ratio</strong><br>{{ $holidayRatio }}x</td>
                <td><strong>Holiday Pay</strong><br>PKR {{ $holidayPayAmount }}</td>

            </tr>
            <tr>

                <td><strong>Overtime Pay Ratio</strong><br>{{ $overTimeRatio }}x</td>
                <td><strong>Overtime Minutes</strong><br>{{ $totalOverTimeMinutesWorked }} mins</td>
                <td><strong>Overtime Pay</strong><br>PKR {{ $totalOvertimePay }}</td>
                <td><strong>Gazatte Pay</strong><br>PKR {{ $gazattePayAmount }}</td>


            </tr>
            <tr>
                <td><strong>Paid Holiday</strong><br>PKR {{ number_format($paidHolidayAmount, 0) }}
                </td>
                <td><strong>Late Minutes</strong><br>{{ number_format($lateMinutes, 2) }} mins</td>

                <td><strong>Late Cut</strong><br>PKR {{ number_format(($lateMinutes / 60) * $salaryPerHour, 0) }}</td>
                {{-- <td><strong>Initial Salary</strong><br>PKR
                    {{ number_format($actualSalaryEarned, 0) }}</td> --}}
                <td><strong>Advance Deduction</strong><br>PKR
                    {{ number_format($salary->advance_deducted, 0) }}</td>


            </tr>
            <tr>
                <td><strong>Miss Scan Days Counted</strong><br>
                    {{ $missDeductDays }}</td>
                <td><strong>Miss Scan Deduct Amount</strong><br>PKR
                    {{ number_format($missAmount, 0) }}</td>
                <td><strong>Loan Deduction</strong><br>PKR
                    {{ number_format($salary->loan_deducted, 0) }}</td>
                <td><strong>Sandwich Leave</strong><br>
                    {{ $sandWich }}</td>

                </td>
            </tr>
            <tr>
                <td><strong>Total Worked Days</strong><br>{{ $workedDays }} days</td>
                <td><strong>Holiday Over Mins</strong><br>{{ $holidayOverMins }} mins</td>
                <td><strong>Early In Mins</strong><br>{{ number_format(array_sum($earlyMinutes), 2) }} mins</td>
                <td><strong>Early Out Mins</strong><br>{{ number_format(array_sum($earlyOutMinutes), 2) }} mins</td>


            </tr>
            <tr>
                <td><strong>Early Cut Amount</strong><br>PKR {{ number_format($earlyCutAmount, 2) }}</td>
                <td class="table-success"><strong>Final Salary</strong><br>PKR
                    {{ number_format(max($actualSalaryEarned - $salary->advance_deducted - $salary->loan_deducted, 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

@props([
    'month',
    'employee',
    'holidays',
    'workingDays',
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
                <td><strong>Hours Worked</strong><br>{{ number_format($totalHoursWorked, 2) }} hours
                </td>
                <td><strong>Salary Per Hour</strong><br>PKR {{ number_format($salaryPerHour, 2) }}</td>

            </tr>
            <tr>
                <td><strong>Employee Holidays</strong> <br>{{ implode(', ', $holidays) }}
                </td>
                <td><strong>Holiday Hours</strong><br>{{ number_format($totalHolidayHoursWorked, 2) }}
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
                <td><strong>Holiday Amount (Including Paid)</strong><br>PKR {{ number_format($paidHolidayAmount, 0) }}
                </td>
                <td><strong>Late Minutes</strong><br>{{ $lateMinutes }} mins</td>

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
                <td class="table-success"><strong>Final Salary</strong><br>PKR
                    {{ number_format(max($actualSalaryEarned - $salary->advance_deducted - $salary->loan_deducted, 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

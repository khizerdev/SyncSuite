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
                <td class="bg-light"><strong>Employee Holidays:</strong> <br>{{ implode(', ', $holidays) }}
                </td>
                <td class="bg-light"><strong>Total Working Days</strong><br>{{ $workingDays }} days</td>
                <td class="bg-light"><strong>Expected Working Hours</strong><br>{{ $totalExpectedWorkingDays }} hours
                </td>
                <td class="bg-light"><strong>Hours Worked</strong><br>{{ number_format($totalHoursWorked, 2) }} hours
                </td>

            </tr>
            <tr>
                <td><strong>Holiday Hours</strong><br>{{ number_format($totalHolidayHoursWorked, 2) }}
                    hours</td>
                <td><strong>Salary Per Hour</strong><br>PKR {{ number_format($salaryPerHour, 2) }}</td>
                <td><strong>Holiday Pay Ratio</strong><br>{{ $holidayRatio }}x</td>
                <td><strong>Overtime Pay Ratio</strong><br>{{ $overTimeRatio }}x</td>
            </tr>
            <tr>
                <td><strong>Holiday Amount</strong><br>PKR {{ number_format($paidHolidayAmount, 0) }}</td>

                <td><strong>Overtime Minutes</strong><br>{{ $totalOverTimeMinutesWorked }} mins</td>
                <td><strong>Overtime Pay</strong><br>PKR {{ $totalOvertimePay }}</td>
                <td><strong>Late Minutes</strong><br>{{ $lateMinutes }} mins</td>

            </tr>
            <tr>
                <td><strong>Late Cut</strong><br>PKR {{ number_format(($lateMinutes / 60) * $salaryPerHour, 0) }}</td>
                <td><strong>Initial Salary</strong><br>PKR
                    {{ number_format($actualSalaryEarned, 0) }}</td>
                <td><strong>Advance Deduction</strong><br>PKR
                    {{ number_format($salary->advance_deducted, 0) }}</td>
                <td><strong>Loan Deduction</strong><br>PKR
                    {{ number_format($salary->loan_deducted, 0) }}</td>


            </tr>
            <tr>
                <td><strong>Gazatte Pay</strong><br>PKR {{ $gazattePayAmount }}</td>

                <td class="table-success"><strong>Final Salary</strong><br>PKR
                    {{ number_format(max($actualSalaryEarned - $salary->advance_deducted - $salary->loan_deducted, 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

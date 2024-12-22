@props([
    'month',
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
    'salary',
])

<div class="col-md-6">
    <h3>Salary Details for {{ $month }} 2024</h3>
    <p>Employee Holidays: {{ implode(', ', $holidays) }}</p>
    <p>Total Working Days: {{ $workingDays }} days</p>
    <p>Total Expected Working Hours: {{ $totalExpectedWorkingDays }} hours</p>
    <p>No of hours worked: {{ number_format($totalHoursWorked, 2) }} hours</p>
    <p>Total Holiday Hours Worked: {{ number_format($totalHolidayHoursWorked, 2) }} hours</p>
    <p>Salary Per Hour: PKR {{ number_format($salaryPerHour, 2) }}</p>
    <p>Holiday Pay Ratio: {{ $holidayRatio }}x</p>
    <p>Overtime Pay Ratio: {{ $overTimeRatio }}x</p>
    <p>Total Overtime Hours Worked: {{ $totalOverTimeHoursWorked }} hours</p>
    <p>Total Overtime Minutes Worked: {{ $totalOverTimeMinutesWorked }} minutes</p>
    <p>Total Overtime Pay: PKR {{ $totalOvertimePay }}</p>
    <p>Late Minutes: {{ $lateMinutes }} minutes</p>
    <p>Late Cut Amount: PKR {{ number_format(($lateMinutes / 60) * $salaryPerHour, 0) }}</p>
    <p>Salary Got: {{ number_format($actualSalaryEarned, 0) }}</p>
    <p>Advance amount: {{ number_format($salary->advance_deducted, 0) }}</p>
    <p>Loan amount: {{ number_format($salary->loan_deducted, 0) }}</p>
    <p>Salary Earned: PKR
        {{ number_format(max($actualSalaryEarned - $salary->advance_deducted - $salary->loan_deducted, 0), 2) }}</p>
</div>

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Salary Reports</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary mr-2" id="printAllBtn" onclick="window.print();">
                        <i class="fas fa-print"></i> Print All Records
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        @foreach ($results as $item)
                            @php
                                $salary = $item['salary'];
                                $attendance = $item['attendance'];
                                $result = $item['salary_data'];

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

                            <div class="card-header printable-section">
                                <div class="row">
                                    <div class="col-md-11">
                                        <h5>Salary Details for {{ $result['employee']->name }} - {{ $month }} 2025
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <table id="salaryTable-{{ $loop->index }}"
                                    class="salary-table table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Employee Name</td>
                                            <td>{{ $result['employee']->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Month</td>
                                            <td>{{ $month }}</td>
                                        </tr>
                                        <tr>
                                            <td>Early Cut Amount</td>
                                            <td>{{ number_format($result['earlyOutCutAmount'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Early Minutes (Check-in)</td>
                                            <td>{{ number_format(array_sum($attendance['earlyCheckinMinutes']), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Early Minutes (Check-out)</td>
                                            <td>{{ number_format(array_sum($attendance['earlyCheckoutMinutes']), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Holidays</td>
                                            <td>{{ implode(', ', $result['holidays']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Working Days</td>
                                            <td>{{ $result['workingDays'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Worked Days</td>
                                            <td>{{ $result['totalWorkedDays'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Expected Working Days</td>
                                            <td>{{ $salary->expected_hours }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Hours Worked</td>
                                            <td>{{ $result['totalHoursWorked'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Holiday Hours Worked</td>
                                            <td>{{ $result['holidayHours'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Salary Per Hour</td>
                                            <td>{{ number_format($result['salaryPerHour'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Paid Holiday Amount</td>
                                            <td>{{ number_format($result['normalHolidayPay'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Gazatte Pay Amount</td>
                                            <td>{{ $result['gazattePay'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Holiday Pay Amount</td>
                                            <td>{{ $result['holidayPay'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Holiday Ratio</td>
                                            <td>{{ $salary->holiday_pay_ratio }}</td>
                                        </tr>
                                        <tr>
                                            <td>Over Time Ratio</td>
                                            <td>{{ $salary->overtime_pay_ratio }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Over Time Hours Worked</td>
                                            <td>{{ $salary->overtime_hours }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Over Time Minutes Worked</td>
                                            <td>{{ $result['totalOvertimeMinutes'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Overtime Pay</td>
                                            <td>{{ number_format($result['totalOvertimePay'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Late Minutes</td>
                                            <td>{{ array_sum($attendance['lateMinutes']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Actual Salary Earned</td>
                                            <td>{{ number_format($result['actualSalaryEarned'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Miss Deduct Days</td>
                                            <td>{{ $result['missDeductDays'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Miss Amount</td>
                                            <td>{{ number_format($result['missAmount'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Holiday Over Minutes</td>
                                            <td>{{ $result['holidayOverMins'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Sandwich Deduction</td>
                                            <td>{{ $result['sandwichDeduct'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Over Minutes (Auto Shift)</td>
                                            <td>{{ number_format(array_sum($result['overMinutesOfAutoShift']), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize DataTables for each table
            // $('.salary-table').each(function() {
            //     $(this).DataTable({
            //         "paging": true,
            //         "lengthChange": true,
            //         "searching": true,
            //         "ordering": true,
            //         "info": true,
            //         "autoWidth": false,
            //         "responsive": true,
            //         "pageLength": 10,
            //         "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            //         "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            //               "<'row'<'col-sm-12'tr>>" +
            //               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            //         "buttons": []
            //     });
            // });

            // Print all records
            // $('#printAllBtn').click(function() {
            //     // Create a new window for printing
            //     var printWindow = window.open('', '', 'height=600,width=800');

            //     // Start building the HTML content
            //     var htmlContent = `
        //         <html>
        //             <head>
        //                 <title>Salary Reports</title>
        //                 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        //                 <style>
        //                     @page { size: auto; margin: 5mm; }
        //                     body { padding: 20px; }
        //                     .print-section { margin-bottom: 30px; page-break-after: always; }
        //                     .print-section:last-child { page-break-after: auto; }
        //                     table { width: 100%; margin-bottom: 1rem; border-collapse: collapse; }
        //                     table td, table th { padding: 0.75rem; vertical-align: top; border: 1px solid #dee2e6; }
        //                     table thead th { vertical-align: bottom; border-bottom: 2px solid #dee2e6; }
        //                 </style>
        //             </head>
        //             <body>
        //                 <div class="container">
        //                     <h1 class="text-center mb-4">Salary Reports</h1>
        //     `;

            //     // Add each table's content
            //     $('.printable-section').each(function() {
            //         // Get the original table HTML before DataTable initialization
            //         var tableHtml = $(this).find('table').prop('outerHTML');

            //         // Get the card header
            //         var cardHeader = $(this).find('.card-title').text();

            //         // Add to HTML content
            //         htmlContent += `
        //             <div class="print-section">
        //                 <h3>${cardHeader}</h3>
        //                 ${tableHtml}
        //             </div>
        //         `;
            //     });

            //     // Close HTML content
            //     htmlContent += `
        //                 </div>
        //             </body>
        //         </html>
        //     `;

            //     // Write the content to the print window
            //     printWindow.document.open();
            //     printWindow.document.write(htmlContent);
            //     printWindow.document.close();

            //     // Wait for content to load before printing
            //     setTimeout(function() {
            //         printWindow.focus();
            //         printWindow.print();
            //     }, 500);
            // });
        });
    </script>
@endsection

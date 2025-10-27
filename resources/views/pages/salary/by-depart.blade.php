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
                        <div class="card-header">
                            <h5>Employee Salary Details</h5>
                        </div>
                        <div class="card-body">
                            @php
                                use Carbon\Carbon;

                                $startDay = 1;
                                $startDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
                                $endDate = $startDate->copy()->endOfMonth();

                                if ($period === 'first_half') {
                                    $startDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
                                    $endDate = Carbon::createFromDate($currentYear, $currentMonth, 15);
                                } elseif ($period === 'second_half') {
                                    $startDate = Carbon::createFromDate($currentYear, $currentMonth, 16);
                                    $endDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth();
                                } elseif ($period === 'full_month') {
                                    $startDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
                                    $endDate = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth();
                                }

                                // Function to add suffix to day (1st, 2nd, 3rd, etc.)
                                function dayWithSuffix($day)
                                {
                                    if (!in_array($day % 100, [11, 12, 13])) {
                                        switch ($day % 10) {
                                            case 1:
                                                return $day . 'st';
                                            case 2:
                                                return $day . 'nd';
                                            case 3:
                                                return $day . 'rd';
                                        }
                                    }
                                    return $day . 'th';
                                }

                                $formattedRange =
                                    dayWithSuffix($startDate->day) .
                                    ' ' .
                                    $startDate->format('F Y') .
                                    ' to ' .
                                    dayWithSuffix($endDate->day) .
                                    ' ' .
                                    $endDate->format('F Y');
                            @endphp

                            <p>Period: {{ $formattedRange }}</p>
                            <!--<p>Date Range: {{ $formattedRange }}</p>-->


                            <p>Department: {{ $depart->name }}</p>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="salaryDataTable" class="table table-bordered table-striped" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Designation</th>
            <th>WD</th>
            <th>Gross Salary</th>
            <th>Allowance</th>
            <th class="d-print-none">Month</th>
            <th class="d-print-none">Adv Deducted</th>
            <th colspan="3">Deduction</th>
            <th class="d-print-none">EC AMT</th>
            <th class="d-print-none">EM (in)</th>
            <th class="d-print-none">Holidays</th>
            <th class="d-print-none">EWD</th>
            <th class="d-print-none">EWH</th>
            <th class="d-print-none">THW</th>
            <th class="d-print-none">HHW</th>
            <th class="d-print-none">AMT Per Hour</th>
            <th class="d-print-none">Holiday Ratio</th>
            <th class="d-print-none">OT Ratio</th>
            <th class="d-print-none">OT Hours</th>
            <th class="d-print-none">OT Minutes</th>
            <th class="d-print-none">Total Overtime Pay</th>
            <th>EM (out)</th>
            <th>Late Minutes</th>
            <th>Total Cut</th>
            <th>Actual Salary Earned</th>
            <th class="d-print-none">Miss Deduct Days</th>
            <th class="d-print-none">Miss Amount</th>
            <th class="d-print-none">Holiday Over Minutes</th>
            <th class="d-print-none">Sandwich Deduction</th>
            <th class="signature-cell">Employee Signature</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th>Advance</th>
            <th>Loan</th>
            <th>Other deduct</th>
            <th></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
            <th class="d-print-none"></th>
        </tr>
        @php
            // Initialize totals
            $totals = [
                'totalWorkedDays' => 0,
                'grossSalary' => 0,
                'allowance' => 0,
                'advanceDeducted' => 0,
                'advance' => 0,
                'loan' => 0,
                'otherDeduct' => 0,
                'earlyOutCut' => 0,
                'earlyCheckin' => 0,
                'workingDays' => 0,
                'expectedHours' => 0,
                'totalHoursWorked' => 0,
                'holidayHours' => 0,
                'salaryPerHour' => 0,
                'overtimeHours' => 0,
                'overtimeMinutes' => 0,
                'overtimePay' => 0,
                'earlyCheckout' => 0,
                'lateMinutes' => 0,
                'totalCut' => 0,
                'actualSalary' => 0,
                'missDeductDays' => 0,
                'missAmount' => 0,
                'holidayOverMins' => 0,
                'sandwichDeduct' => 0,
            ];
        @endphp
        
        @foreach ($results as $item)
            @php
                $salary = $item['salary'];
                $attendance = $item['attendance'];
                $result = $item['salary_data'];

                $months = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December',
                ];
                $month = $months[$salary->month - 1];
                
                // Calculate gross salary
                $shifts = $result['employee']->shift;

                $start = DateTime::createFromFormat('H:i:s', $shifts['start_time']);
                $end = DateTime::createFromFormat('H:i:s', $shifts['end_time']);
                
                // If end time is earlier than start time, assume it's next day
                if ($end < $start) {
                    $end->modify('+1 day');
                }
                
                $interval = $start->diff($end);
                $totalHours = $interval->h + ($interval->i / 60) + ($interval->s / 3600);
                $totalHoursFormatted = number_format($totalHours, 2);
                
                // Add to totals (convert all to float to avoid non-numeric errors)
                $totals['totalWorkedDays'] += floatval($result['totalWorkedDays'] ?? 0);
                $totals['grossSalary'] = round(floatval($result['salaryPerHour'] ?? 0) * $totalHoursFormatted * floatval($result['totalWorkedDays'] ?? 0));
                $totals['allowance'] += floatval(str_replace(',', '', $result['totalOvertimePay'] ?? 0));
                $totals['advanceDeducted'] += floatval($salary->advance_deducted ?? 0);
                $totals['advance'] += floatval($salary->advance_deducted ?? 0);
                $totals['loan'] += floatval($salary->loan_deducted ?? 0);
                $totals['otherDeduct'] += floatval($result['deduction'] ?? 0);
                $totals['earlyOutCut'] += floatval($result['earlyOutCutAmount'] ?? 0);
                $totals['earlyCheckin'] += floatval(array_sum($attendance['earlyCheckinMinutes'] ?? []));
                $totals['workingDays'] += floatval($result['workingDays'] ?? 0);
                $totals['expectedHours'] += floatval($salary->expected_hours ?? 0);
                $totals['totalHoursWorked'] += floatval($result['totalHoursWorked'] ?? 0);
                $totals['holidayHours'] += floatval($result['holidayHours'] ?? 0);
                $totals['salaryPerHour'] += floatval($result['salaryPerHour'] ?? 0);
                $totals['overtimeHours'] += floatval($salary->overtime_hours ?? 0);
                $totals['overtimeMinutes'] += floatval($result['totalOvertimeMinutes'] ?? 0);
                $totals['overtimePay'] += floatval($result['totalOvertimePay'] ?? 0);
                $totals['earlyCheckout'] += floatval(array_sum($attendance['earlyCheckoutMinutes'] ?? []));
                $totals['lateMinutes'] += floatval(array_sum($attendance['lateMinutes'] ?? []));
                $totals['totalCut'] += floatval($result['lateCutAmount'] ?? 0) + floatval($result['earlyOutCutAmount'] ?? 0);
                $totals['actualSalary'] += floatval($result['actualSalaryEarned'] ?? 0) - floatval($salary->advance_deducted ?? 0) - floatval($salary->loan_deducted ?? 0);
                $totals['missDeductDays'] += floatval($result['missDeductDays'] ?? 0);
                $totals['missAmount'] += floatval($result['missAmount'] ?? 0);
                $totals['holidayOverMins'] += floatval($result['holidayOverMins'] ?? 0);
                $totals['sandwichDeduct'] += floatval($result['sandwichDeduct'] ?? 0);
            @endphp
            <tr>
                <td><strong>{{ $result['employee']->name }}</strong></td>
                <td><strong>{{ $result['employee']->designation }}</strong></td>
                <td class="text-center">{{ $result['totalWorkedDays'] }}</td>
                <td>{{ $totals['grossSalary'] }}</td>
                <td class="text-right">{{ str_replace(',', '', $result['totalOvertimePay']) }}</td>
                <td class="d-print-none">{{ $month }}</td>
                <td class="d-print-none">{{ $salary->advance_deducted }}</td>
                <td>{{ $salary->advance_deducted }}</td>
                <td>{{ $salary->loan_deducted }}</td>
                <td>{{ number_format($result['deduction'], 2) }}</td>
                <td class="text-right d-print-none">{{ number_format($result['earlyOutCutAmount'], 2) }}</td>
                <td class="text-right d-print-none">{{ number_format(array_sum($attendance['earlyCheckinMinutes']), 2) }}</td>
                <td class="d-print-none">{{ !empty($result['holidays']) ? implode(', ', $result['holidays']) : 'No Holidays' }}</td>
                <td class="text-center d-print-none">{{ $result['workingDays'] }}</td>
                <td class="text-center d-print-none">{{ $salary->expected_hours }}</td>
                <td class="text-right d-print-none">{{ $result['totalHoursWorked'] }}</td>
                <td class="text-right d-print-none">{{ $result['holidayHours'] }}</td>
                <td class="text-right d-print-none">{{ number_format($result['salaryPerHour'], 2) }}</td>
                <td class="text-center d-print-none">{{ $salary->holiday_pay_ratio }}</td>
                <td class="text-center d-print-none">{{ $salary->overtime_pay_ratio }}</td>
                <td class="text-right d-print-none">{{ $salary->overtime_hours }}</td>
                <td class="text-right d-print-none">{{ $result['totalOvertimeMinutes'] }}</td>
                <td class="text-right d-print-none">{{ number_format($result['totalOvertimePay'], 2) }}</td>
                <td class="text-right">{{ number_format(array_sum($attendance['earlyCheckoutMinutes']), 2) }}</td>
                <td class="text-right">{{ number_format(array_sum($attendance['lateMinutes']), 2) }}</td>
                <td class="text-right">{{ number_format($result['lateCutAmount'] + $result['earlyOutCutAmount'], 2) }}</td>
                <td class="text-right"><strong>{{ number_format($result['actualSalaryEarned'] - $salary->advance_deducted - $salary->loan_deducted, 2) }}</strong></td>
                <td class="text-center d-print-none">{{ $result['missDeductDays'] }}</td>
                <td class="text-right d-print-none">{{ number_format($result['missAmount'], 2) }}</td>
                <td class="text-right d-print-none">{{ number_format($result['holidayOverMins'], 2) }}</td>
                <td class="text-right d-print-none">{{ $result['sandwichDeduct'] }}</td>
                <td class="signature-cell"><div class="signature-box"></div></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="2" class="text-right"><strong>TOTAL:</strong></td>
            <td class="text-center">{{ $totals['totalWorkedDays'] }}</td>
            <td class="text-right">{{ number_format($totals['grossSalary'], 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($totals['allowance'], 2) }}</td>
            <td class="d-print-none"></td>
            <td class="text-right d-print-none">{{ number_format($totals['advanceDeducted'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['advance'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['loan'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['otherDeduct'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['earlyOutCut'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['earlyCheckin'], 2) }}</td>
            <td class="d-print-none"></td>
            <td class="text-center d-print-none">{{ $totals['workingDays'] }}</td>
            <td class="text-center d-print-none">{{ number_format($totals['expectedHours'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['totalHoursWorked'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['holidayHours'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['salaryPerHour'], 2) }}</td>
            <td class="d-print-none"></td>
            <td class="d-print-none"></td>
            <td class="text-right d-print-none">{{ number_format($totals['overtimeHours'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['overtimeMinutes'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['overtimePay'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['earlyCheckout'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['lateMinutes'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['totalCut'], 2) }}</td>
            <td class="text-right"><strong>{{ number_format($totals['actualSalary'], 2) }}</strong></td>
            <td class="text-center d-print-none">{{ $totals['missDeductDays'] }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['missAmount'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['holidayOverMins'], 2) }}</td>
            <td class="text-right d-print-none">{{ number_format($totals['sandwichDeduct'], 2) }}</td>
            <td class="signature-cell"></td>
        </tr>
    </tfoot>
</table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#salaryDataTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": true,
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "order": [
                    [0, "asc"]
                ], // Sort by employee name by default
                "columnDefs": [{
                    "targets": [6, 7, 8, 9], // Salary columns
                    "className": "text-right"
                }],
                "language": {
                    "search": "Search employees:",
                    "lengthMenu": "Show _MENU_ employees per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ employees",
                    "infoEmpty": "Showing 0 to 0 of 0 employees",
                    "infoFiltered": "(filtered from _MAX_ total employees)"
                },
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print Table',
                        className: 'btn btn-info btn-sm'
                    }
                ]
            });

            // Custom search functionality
            $('#salaryDataTable_filter input').attr('placeholder', 'Search by employee name, month, etc...');
        });


        // Print all functionality
        // $('#printAllBtn').on('click', function() {
        //     window.print();
        // });
    </script>

    <style>
     /* Custom styles for signature boxes and increased row height */
        .signature-cell {
            width: 120px;
            min-width: 120px;
            text-align: center;
            vertical-align: middle;
            padding: 15px 8px !important;
        }
        
        .signature-box {
            border: 1px solid #333;
            height: 40px;
            width: 100px;
            margin: 0 auto;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            font-size: 10px;
            padding: 2px;
            background-color: #f9f9f9;
        }
        
        /* Increase row height for all rows */
        #salaryDataTable tbody tr {
            height: 60px;
        }
        
        #salaryDataTable tbody td {
            padding: 15px 8px !important;
            vertical-align: middle;
        }
        
        #salaryDataTable thead th {
            padding: 12px 8px !important;
            vertical-align: middle;
        }
        
        /* Print specific styles */
        @media print {
            .signature-box {
                border: 1px solid #000 !important;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            #salaryDataTable tbody tr {
                height: 70px !important;
            }
            
            #salaryDataTable tbody td {
                padding: 18px 8px !important;
            }
            
            .signature-cell {
                padding: 18px 8px !important;
            }
        }
        
        /* Make table responsive */
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Ensure table maintains structure */
        #salaryDataTable {
            white-space: nowrap;
        }
        @media print {

            .card-header,
            .btn,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate,
            .dt-buttons {
                display: none !important;
            }

            .table {
                font-size: 12px;
            }

            .table td,
            .table th {
                padding: 4px !important;
            }
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 12px;
            }

            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection

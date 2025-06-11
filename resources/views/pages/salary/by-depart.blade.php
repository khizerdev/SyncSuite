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
                            <div class="table-responsive">
                                <table id="salaryDataTable" class="table table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Month</th>
                                            <th>Working Days</th>
                                            <th>Worked Days</th>
                                            <th>Total Hours Worked</th>
                                            <th>Salary Per Hour</th>
                                            <th>Actual Salary Earned</th>
                                            <th>Early Cut Amount</th>
                                            <th>Miss Amount</th>
                                            <th>Total Overtime Pay</th>
                                            <th>Late Minutes</th>

                                        </tr>
                                    </thead>
                                    <tbody>
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
                                            <tr>
                                                <td>{{ $result['employee']->name }}</td>
                                                <td>{{ $month }}</td>
                                                <td>{{ $result['workingDays'] }}</td>
                                                <td>{{ $result['totalWorkedDays'] }}</td>
                                                <td>{{ $result['totalHoursWorked'] }}</td>
                                                <td>{{ number_format($result['salaryPerHour'], 2) }}</td>
                                                <td>{{ number_format($result['actualSalaryEarned'], 2) }}</td>
                                                <td>{{ number_format($result['earlyOutCutAmount'], 2) }}</td>
                                                <td>{{ number_format($result['missAmount'], 2) }}</td>
                                                <td>{{ number_format($result['totalOvertimePay'], 2) }}</td>
                                                <td>{{ array_sum($attendance['lateMinutes']) }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Modals -->
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
    @endforeach
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

        // Function to print modal content
        function printModal(modalId) {
            var printContents = document.getElementById(modalId).querySelector('.modal-body').innerHTML;
            var originalContents = document.body.innerHTML;

            var printWindow = window.open('', '_blank');
            printWindow.document.write(`
        <html>
            <head>
                <title>Salary Report Details</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100% !important; }
                    .table td, .table th { padding: 8px; border: 1px solid #dee2e6; }
                    @media print {
                        .btn { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    ${printContents}
                </div>
            </body>
        </html>
    `);
            printWindow.document.close();
            printWindow.print();
        }

        // Print all functionality
        $('#printAllBtn').on('click', function() {
            window.print();
        });
    </script>

    <style>
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

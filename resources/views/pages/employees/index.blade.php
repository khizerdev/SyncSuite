@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">
                                <h3 class="card-title">Employees</h3>
                            </div>
                            <div class="col-6 text-right">
                                <a class="btn btn-primary" href="{{ route('employees.create') }}">Add New Employee</a>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="department-filter">Filter by Department:</label>
                                    <select id="department-filter" class="form-control">
                                        <option value="">All Departments</option>
                                        @foreach (App\Models\Department::all() as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="type-filter">Filter by Type:</label>
                                    <select id="type-filter" class="form-control">
                                        <option value="">All Types</option>
                                        @foreach (App\Models\EmployeeType::all() as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Contact</th>
                                        <th>Department</th>
                                        <th>Type</th>
                                        <th>Designation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Payroll Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="attdForm" action="{{ url('employees/payroll') }}" method="GET">

                        <div class="form-group mb-2">
                            <input type="text" class="form-control" id="employeeName" readonly>
                        </div>

                        <div class="form-group mb-2">
                            <label for="year" class="sr-only">Select Year:</label>
                            <select class="form-control" name="year" id="year" required>
                                <option value="" disabled selected>Select Year</option>
                                @foreach (range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label for="month" class="sr-only">Select Month:</label>
                            <select class="form-control" name="month" id="month" required>
                                <option value="" disabled selected>Select Month</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}">
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label for="duration" class="sr-only">Select Duration:</label>
                            <select class="form-control" name="duration" id="duration" required>
                                <option value="" disabled selected>Select Duration</option>
                                <option value="first_half">First Half</option>
                                <option value="second_half">Second Half</option>
                                <option value="full_month">Full Month</option>
                            </select>
                        </div>

                        <div class="form-group mb-2" id="payrollForm">

                        </div>

                        <button type="submit" class="btn btn-primary mb-2">View Salary</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            // DataTable initialization
            var dataTable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employees.index') }}",
                    data: function(d) {
                        d.department_id = $('#department-filter').val();
                        d.type_id = $('#type-filter').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'contact_number',
                        name: 'contact_number'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'type_name',
                        name: 'type_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'designation',
                        name: 'designation',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table').on('click', '.btn-show-employee', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                $('#employeeName').val(employeeName);
                var employeeId = $(this).data('employee-id');
                $('#attdForm').attr('action', `{{ url('employees/payroll') }}/${employeeId}`);

                $('#loanField').remove();
                $('#loanCheckbox').remove();

                var apiUrl = "{{ url('/get-employee-loan') }}";

                //   $.ajax({
                //   url: apiUrl,
                //     method: 'GET',
                //     data: { employee_id: employeeId },
                //     success: function(response) {
                //         if (response.status === 'success') {
                //             var loanFieldHtml = `
            //               <div class="form-group" id="loanField">
            //                   <label>Loan Balance:</label>
            //                   <input type="text" name="loan_balance" id="loanBalance" class="form-control" readonly value="${response.loan_balance}">
            //               </div>
            //               <div class="form-check" id="loanCheckbox">
            //                   <input class="form-check-input" name="include_loan" type="checkbox" value="1" id="defaultCheck1">
            //                   <label class="form-check-label" for="defaultCheck1">
            //                     Include Loan
            //                   </label>
            //               </div>`;

                //             $('#payrollForm').append(loanFieldHtml);
                //         } 
                //     }
                // });
            });

            $('#department-filter').on('change', function() {
                dataTable.draw();
            });

            $('#type-filter').on('change', function() {
                dataTable.draw();
            });


        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#year').change(function() {
                var selectedYear = $(this).val();
                var currentMonth = new Date().getMonth() + 1;

                $('#month option').each(function() {
                    var monthValue = parseInt($(this).val());
                    if (selectedYear == new Date().getFullYear() && monthValue > currentMonth) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            });

            $('#year').trigger('change');
        });
    </script>
@endsection

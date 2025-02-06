@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center justify-content-between">
                            <div class="col-10">
                                <h3 class="card-title">Salary History</h3>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select id="department" class="form-control">
                                        <option value="">Select Department</option>
                                        @foreach (\App\Models\Department::all() as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="month" class="form-control">
                                        <option value="">Select Month</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="year" class="form-control">
                                        <option value="">Select Year</option>
                                        @for ($i = date('Y'); $i >= 2019; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button id="filter" class="btn btn-primary">Filter</button>
                                </div>
                            </div>

                            <div class="card-body">
                                @role('hr|super-admin')
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Name</th>
                                                    <th>Overtime</th>
                                                    <th>Late Amount</th>
                                                    <th>Loan</th>
                                                    <th>Advance</th>
                                                    <th>Salary</th>
                                                    <th>Period</th>
                                                    <th>Month/Year</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                @else
                                    <table class="table table-bordered" id="table">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Period</th>
                                                <th>Start Date</th>
                                                <th>Start End</th>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                @endrole
                            </div>

                        </div>

                    </div>
                </div>

            </div>
    </section>
@endsection

@section('script')
    @role('hr|super-admin')
        <script type="text/javascript">
            $(document).ready(function() {
                var dataTable = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('salaries.index') }}",
                        data: function(d) {
                            d.department = $('#department').val();
                            d.month = $('#month').val();
                            d.year = $('#year').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'employee_name',
                            name: 'employee_name'
                        },
                        {
                            data: 'overtime',
                            name: 'overtime'
                        },
                        {
                            data: 'late',
                            name: 'late'
                        },
                        {
                            data: 'loan',
                            name: 'loan'
                        },
                        {
                            data: 'advance',
                            name: 'advance'
                        },
                        {
                            data: 'salary',
                            name: 'salary'
                        },
                        {
                            data: 'period',
                            name: 'period'
                        },
                        {
                            data: 'month_year',
                            name: 'month_year'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                $('#filter').on('click', function() {
                    dataTable.ajax.reload();
                });
            });
        </script>
    @else
        <script type="text/javascript">
            $(document).ready(function() {
                var dataTable = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('salaries.index') }}",
                        data: function(d) {
                            d.department = $('#department').val();
                            d.month = $('#month').val();
                            d.year = $('#year').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'employee_name',
                            name: 'employee_name'
                        },
                        {
                            data: 'period',
                            name: 'period'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'month',
                            name: 'month'
                        },
                        {
                            data: 'year',
                            name: 'year'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                $('#filter').on('click', function() {
                    dataTable.ajax.reload();
                });
            });
        </script>
    @endrole
@endsection

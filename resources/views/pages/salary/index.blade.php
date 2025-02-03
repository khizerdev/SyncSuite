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
                    ajax: "{{ route('salaries.index') }}",
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

            });
        </script>
    @else
        <script type="text/javascript">
            $(document).ready(function() {
                var dataTable = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('salaries.index') }}",
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

            });
        </script>
    @endrole
@endsection

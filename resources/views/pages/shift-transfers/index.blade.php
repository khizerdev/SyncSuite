@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center justify-content-between">
                            <div class="col-10">
                                <h3 class="card-title">Shift Trasfer</h3>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('shift-transfers.store') }}" method="POST" class="mb-4">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="employee_id">Employee</label>
                                        <select name="employee_ids[]" id="employee_id" class="form-control" multiple
                                            required>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="shift_id">New Shift</label>
                                        <select name="shift_id" id="shift_id" class="form-control" required>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="from_date">From Date</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Create Shift Transfer</button>
                            </form>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Shift</th>
                                        <th>From Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shiftTransfers as $transfer)
                                        <tr>
                                            <td>{{ $transfer->employee->name }}</td>
                                            <td>{{ $transfer->shift->name }}</td>
                                            <td>{{ $transfer->from_date }}</td>
                                            <td>
                                                <form action="{{ route('shift-transfers.destroy', $transfer->id) }}"
                                                    method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (confirm('Are you sure you want to delete this shift transfer?')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#employee_id').select2();
        });

        $(document).ready(function() {
            var dataTable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('shifts.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
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
@endsection

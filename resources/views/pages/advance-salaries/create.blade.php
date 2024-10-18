@extends('layouts.app')

@section('content')
<section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">Branch</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/advance-salaries') }}">View List</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Create Advance Salary</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('advance-salaries.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control" required>
                                    <option value="">Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
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
        $('#employee_id').change(function() {
            var employeeId = $(this).val();
            
            if (employeeId) {
                $.ajax({
                    url: `/employees/calculate-salary-for-advance/${employeeId}`,
                    type: 'GET',
                    success: function(response) {
                        $('#amount').val(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching salary:', error);
                    }
                });
            } else {
                // Clear the amount field if no employee is selected
                $('#amount').val('');
            }
        });
    });
</script>
@endsection
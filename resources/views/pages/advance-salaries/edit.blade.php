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
                            <h3 class="card-title">Edit Advance Salary</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('advance-salaries.update', $advanceSalary->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control" required>
                                        <option value="">Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $advanceSalary->employee_id == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="earnings">Earnings</label>
                                    <input type="number" name="earnings" id="earnings" class="form-control" value="" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" value="{{ $advanceSalary->amount }}" max="{{ $advanceSalary->amount }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $advanceSalary->notes }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
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
            // When the employee is changed
            $('#employee_id').change(function() {
                var employeeId = $(this).val();

                if (employeeId) {
                    $.ajax({
                        url: `/employees/calculate-salary-for-advance/${employeeId}`,
                        type: 'GET',
                        success: function(response) {
                            $('#earnings').val(response);

                            // Update max value for the amount field
                            $('#amount').attr('max', response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching earnings:', error);
                        }
                    });
                } else {
                    // Clear earnings field and reset max value for the amount field
                    $('#earnings').val('');
                    $('#amount').attr('max', 0);
                }
            });

            // Ensure the amount doesn't exceed the earnings value
            $('#amount').on('input', function() {
                var maxAmount = parseFloat($('#earnings').val());
                if ($(this).val() > maxAmount) {
                    $(this).val(maxAmount);
                }
            });
        });
    </script>
@endsection
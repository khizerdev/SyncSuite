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
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/loans') }}">View Loans</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Loan</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('loans.update', $loan) }}" method="POST" class="row">
                                @csrf
                                @method('PUT')
                                <div class="mb-3 col-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" required step="0.01" min="0" value="{{ old('amount', $loan->amount) }}">
                                </div>
                                <div class="mb-3 col-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select class="form-control" id="employee_id" name="employee_id" required>
                                        <option value="">Select an employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}" {{ $employee->id == $loan->employee_id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-3">
                                    <label for="salary" class="form-label">Salary</label>
                                    <input type="number" class="form-control" id="salary" name="salary" required step="0.01" min="0" readonly value="{{ old('salary', $loan->salary) }}">
                                </div>
                                <div class="mb-3 col-3">
                                    <label for="months" class="form-label">Number of Months</label>
                                    <input type="number" class="form-control" id="months" name="months" required min="1" value="{{ old('months', $loan->months) }}">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Update Loan</button>
                                <a href="{{ route('loans.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                                                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employee_id');
    const salaryInput = document.getElementById('salary');

    function updateSalary() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        const salary = selectedOption.getAttribute('data-salary');
        salaryInput.value = salary || '';
    }

    employeeSelect.addEventListener('change', updateSalary);
    
    // Set initial salary value
    updateSalary();
});
</script>
@endsection
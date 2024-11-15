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
                            <h3 class="card-title">Create Loan</h3>
                        </div>
                        <div class="card-body">
                             <form action="{{ route('loans.store') }}" method="POST" class="row">
        @csrf
        
        <div class="mb-3 col-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <option value="">Select an employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3 col-3">
            <label for="salary" class="form-label">Salary</label>
            <input type="number" class="form-control" id="salary" name="salary" required step="0.01" min="0" readonly>
        </div>
        <div class="mb-3 col-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required step="0.01" min="0">
        </div>
        <div class="mb-3 col-3">
            <label for="months" class="form-label">Number of Months</label>
            <input type="number" class="form-control" id="months" name="months" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
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

    employeeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const salary = selectedOption.getAttribute('data-salary');
        salaryInput.value = salary || '';
    });
});
</script>
@endsection
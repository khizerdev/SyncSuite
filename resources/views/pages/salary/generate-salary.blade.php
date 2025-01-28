@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center justify-content-between">
                            <div class="col-10">
                                <h3 class="card-title">Generate Salary</h3>
                            </div>

                        </div>

                        <div class="card-body">
                            <form action="{{ route('generate-salary.process') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="generate_for">Generate Salary For:</label>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" value="employee" type="radio"
                                            id="generateForEmployee" name="generate_for" required onclick="toggleFields()">
                                        <label for="generateForEmployee" class="custom-control-label">By Employee</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" value="department" type="radio"
                                            id="generateForDepartment" name="generate_for" required
                                            onclick="toggleFields()">
                                        <label for="generateForDepartment" class="custom-control-label">By
                                            Department</label>
                                    </div>
                                </div>

                                <div class="form-group" id="employeeField" style="display: none;">
                                    <label for="employee_id">Select Employee:</label>
                                    <select name="employee_id" id="employee_id" class="form-control mb-2">
                                        <option value="">Select Employee</option>
                                        @foreach (\App\Models\Employee::all() as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="departmentField" style="display: none;">
                                    <label for="department_id">Select Department:</label>
                                    <select name="department_id" id="department_id" class="form-control mb-2">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="month">Select Month:</label>
                                    <select class="form-control" name="month" id="month" required>
                                        <option value="" disabled selected>Select Month</option>
                                        @foreach (range(1, 12) as $m)
                                            <option value="{{ sprintf('%02d', $m) }}">
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="year">Select Year:</label>
                                    <select class="form-control" name="year" id="year" required>
                                        <option value="" disabled selected>Select Year</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="period">Duration:</label>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" value="first_half" type="radio"
                                            id="customRadio1" name="period" required>
                                        <label for="customRadio1" class="custom-control-label">First 15 Days</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" value="second_half" type="radio"
                                            id="customRadio2" name="period" required>
                                        <label for="customRadio2" class="custom-control-label">Next 15 Days</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" value="full_month" type="radio"
                                            id="customRadio3" name="period" required>
                                        <label for="customRadio3" class="custom-control-label">Full Month</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Generate</button>
                            </form>
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
        function toggleFields() {
            const generateForEmployee = document.getElementById('generateForEmployee');
            const generateForDepartment = document.getElementById('generateForDepartment');
            const employeeField = document.getElementById('employeeField');
            const departmentField = document.getElementById('departmentField');

            if (generateForEmployee.checked) {
                employeeField.style.display = 'block';
                departmentField.style.display = 'none';
            } else if (generateForDepartment.checked) {
                employeeField.style.display = 'none';
                departmentField.style.display = 'block';
            }
        }
    </script>
@endsection

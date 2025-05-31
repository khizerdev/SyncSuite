@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Shift Transfer Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Shift Transfer</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('shift-transfers.store') }}" method="POST" id="shift-transfer-form">
                                @csrf

                                <!-- Department Selection -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="department_id">Department</label>
                                        <select id="department_id" class="form-control">
                                            <option value="">Select Department</option>
                                            @foreach (\App\Models\Department::all() as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Employee Table -->
                                <div id="employee-table-container" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Select Employees</h6>
                                        <div>
                                            <button type="button" id="select-all-btn"
                                                class="btn btn-outline-primary btn-sm">Select All</button>
                                            <button type="button" id="deselect-all-btn"
                                                class="btn btn-outline-secondary btn-sm">Deselect All</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="employees-table">
                                            <thead>
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" id="select-all-checkbox">
                                                    </th>
                                                    <th>Employee ID</th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employees-tbody">
                                                <!-- Employees will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Shift Transfer Details -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label for="shift_id">New Shift</label>
                                        <select name="shift_id" id="shift_id" class="form-control" required>
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->name }}
                                                    ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="from_date">From Date</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Selected Employees Summary -->
                                <div class="mt-3">
                                    <div id="selected-employees-summary" style="display: none;">
                                        <div class="alert alert-info">
                                            <strong>Selected Employees (<span id="selected-count">0</span>):</strong><br>
                                            <span id="selected-employees-list"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input for selected employee IDs -->
                                <input type="hidden" name="employee_ids" id="employee_ids" value="">

                                <button type="submit" class="btn btn-primary mt-3" id="submit-btn" disabled>
                                    Create Shift Transfer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Shift Transfers Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Existing Shift Transfers</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="shift-transfers-table">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Employee ID</th>
                                            <th>Department</th>
                                            <th>New Shift</th>
                                            <th>From Date</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shiftTransfers as $transfer)
                                            <tr>
                                                <td>{{ $transfer->employee->name }}</td>
                                                <td>{{ $transfer->employee->employee_id ?? 'N/A' }}</td>
                                                <td>{{ $transfer->employee->department->name ?? 'N/A' }}</td>
                                                <td>{{ $transfer->shift->name }} ({{ $transfer->shift->start_time }} -
                                                    {{ $transfer->shift->end_time }})</td>
                                                <td>{{ \Carbon\Carbon::parse($transfer->from_date)->format('d M Y') }}</td>
                                                <td>{{ $transfer->created_at->format('d M Y H:i') }}</td>
                                                <td>
                                                    <form action="{{ route('shift-transfers.destroy', $transfer->id) }}"
                                                        method="POST" class="delete-form d-inline">
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
        </div>
    </section>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const employeeTableContainer = document.getElementById('employee-table-container');
            const employeesTableBody = document.getElementById('employees-tbody');
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const selectAllBtn = document.getElementById('select-all-btn');
            const deselectAllBtn = document.getElementById('deselect-all-btn');
            const submitBtn = document.getElementById('submit-btn');
            const employeeIdsInput = document.getElementById('employee_ids');
            const selectedEmployeesSummary = document.getElementById('selected-employees-summary');
            const selectedEmployeesList = document.getElementById('selected-employees-list');
            const selectedCount = document.getElementById('selected-count');

            // Department change handler
            departmentSelect.addEventListener('change', function() {
                const departmentId = this.value;

                if (departmentId) {
                    fetchEmployees(departmentId);
                    employeeTableContainer.style.display = 'block';
                } else {
                    employeeTableContainer.style.display = 'none';
                    clearSelection();
                }
            });

            // Fetch employees by department
            function fetchEmployees(departmentId) {
                fetch(`${baseUrl}/api/employees/by-department/${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        populateEmployeeTable(data.employees);
                    })
                    .catch(error => {
                        console.error('Error fetching employees:', error);
                        alert('Error loading employees. Please try again.');
                    });
            }

            // Populate employee table
            function populateEmployeeTable(employees) {
                employeesTableBody.innerHTML = '';

                employees.forEach(employee => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>
                    <input type="checkbox" class="employee-checkbox" value="${employee.id}" data-name="${employee.name}">
                </td>
                <td>${employee.id || 'N/A'}</td>
                <td>${employee.name}</td>
                <td>${employee.code}</td>
                
            `;
                    employeesTableBody.appendChild(row);
                });

                // Add event listeners to checkboxes
                addCheckboxListeners();
            }

            // Add event listeners to employee checkboxes
            function addCheckboxListeners() {
                const checkboxes = document.querySelectorAll('.employee-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelection);
                });
            }

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.employee-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelection();
            });

            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.employee-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                selectAllCheckbox.checked = true;
                updateSelection();
            });

            deselectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.employee-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                selectAllCheckbox.checked = false;
                updateSelection();
            });

            // Update selection summary
            function updateSelection() {
                const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
                const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
                const selectedNames = Array.from(checkedBoxes).map(cb => cb.getAttribute('data-name'));

                // Update hidden input
                employeeIdsInput.value = JSON.stringify(selectedIds);

                // Update submit button state
                submitBtn.disabled = selectedIds.length === 0;

                // Update summary
                if (selectedIds.length > 0) {
                    selectedCount.textContent = selectedIds.length;
                    selectedEmployeesList.innerHTML = selectedNames.join(', ');
                    selectedEmployeesSummary.style.display = 'block';
                } else {
                    selectedEmployeesSummary.style.display = 'none';
                }

                // Update select all checkbox state
                const allCheckboxes = document.querySelectorAll('.employee-checkbox');
                selectAllCheckbox.checked = allCheckboxes.length > 0 && checkedBoxes.length === allCheckboxes
                .length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < allCheckboxes
                    .length;
            }

            // Clear selection
            function clearSelection() {
                employeeIdsInput.value = '';
                submitBtn.disabled = true;
                selectedEmployeesSummary.style.display = 'none';
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }

            // Form submission
            document.getElementById('shift-transfer-form').addEventListener('submit', function(e) {
                const selectedIds = JSON.parse(employeeIdsInput.value || '[]');

                if (selectedIds.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one employee.');
                    return;
                }

                // Convert JSON array back to individual inputs for Laravel
                const form = this;
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'employee_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
            });

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (confirm('Are you sure you want to delete this shift transfer?')) {
                        this.submit();
                    }
                });
            });

            // Initialize DataTables for existing shift transfers
            if (typeof $.fn.DataTable !== 'undefined') {
                $('#shift-transfers-table').DataTable({
                    responsive: true,
                    order: [
                        [5, 'desc']
                    ], // Order by created date
                    columnDefs: [{
                            orderable: false,
                            targets: [6]
                        } // Disable ordering on action column
                    ]
                });
            }
        });
    </script>
@endsection

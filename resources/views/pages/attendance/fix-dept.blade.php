@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Fixed Department Attendance Report</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('generate-fixed-dept-report') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="department_id">Department</label>
                                        <select class="form-control" id="department_id" name="department_id" required>
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ (isset($departmentId) && $departmentId == $department->id) ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ $date ?? date('Y-m-d') }}" required>
                                    </div>
                                    <div class="form-group col-md-2" style="align-self: flex-end;">
                                        <button type="submit" class="btn btn-primary">Generate Report</button>
                                    </div>
                                </div>
                            </form>
                                        @if(isset($processedEmployees))
                                    <hr>
                                    <h4>Attendance Report for {{ \App\Models\Department::find($departmentId)->name }} on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h4>
                                    
                                    <form id="attendanceForm" method="POST" action="{{ route('delete-day-entries') }}">
    @csrf
    <input type="hidden" name="department_id" value="{{ $departmentId }}">
    <input type="hidden" name="date" value="{{ $date }}">
    
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processedEmployees as $index => $record)
                    <tr>
                        <td>
                            @if($record['has_attendance'])
                                <input type="checkbox" name="employees[]" value="{{ $record['employee']->id }}">
                            @endif
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record['employee']->code }}</td>
                        <td>{{ $record['employee']->name }}</td>
                        <td>
                            @if($record['check_in'])
                                {{ \Carbon\Carbon::parse($record['check_in'])->format('h:i A') }}
                            @else
                                @if($record['has_attendance'])
                                    <span class="text-warning">Error</span>
                                @else
                                    <span class="text-danger">Absent</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($record['check_out'])
                                {{ \Carbon\Carbon::parse($record['check_out'])->format('h:i A') }}
                            @else
                                @if($record['check_in'])
                                    <span class="text-warning">No Check-out</span>
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($record['has_attendance'])
                                <span class="badge badge-success">Present</span>
                            @else
                                <span class="badge badge-danger">Absent</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-danger" id="deleteSelected">
            <i class="fas fa-trash"></i> Delete All Entries for Selected Employees
        </button>
    </div>
</form>
                                @endif
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
        // Select all checkboxes
        $('#selectAll').change(function() {
            $('input[name="employees[]"]').prop('checked', $(this).prop('checked'));
        });
        
        // Form submission confirmation
        $('#attendanceForm').submit(function(e) {
            if($('input[name="employees[]"]:checked').length === 0) {
                e.preventDefault();
                alert('Please select at least one employee');
            } else if(!confirm('Are you sure you want to delete ALL attendance records for the selected employees?')) {
                e.preventDefault();
            }
        });
    });
    </script>
@endsection

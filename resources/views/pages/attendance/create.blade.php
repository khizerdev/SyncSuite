@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Record Attendance</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="employee_id">Select Employee</label>
                                <select id="employee_id" class="form-control" required>
                                    <option value="">Select Employee</option>
                                    @foreach (\App\Models\Employee::all('id', 'name') as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="attendance_date">Select Date</label>
                                <input type="date" id="attendance_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mt-3">
                                <button id="checkInBtn" class="btn btn-success" disabled>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    <span class="btn-text">Check In</span>
                                </button>
                                <button id="checkOutBtn" class="btn btn-warning" disabled>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    <span class="btn-text">Check Out</span>
                                </button>
                            </div>
                            <div id="statusMessage" class="mt-2"></div>
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
            const employeeSelect = $('#employee_id');
            const attendanceDate = $('#attendance_date');
            const checkInBtn = $('#checkInBtn');
            const checkOutBtn = $('#checkOutBtn');
            const statusMessage = $('#statusMessage');
            const attendanceList = $('#attendanceList');

            function setLoading(button, isLoading) {
                const spinner = button.find('.spinner-border');
                const btnText = button.find('.btn-text');

                if (isLoading) {
                    spinner.removeClass('d-none');
                    btnText.addClass('d-none');
                    button.prop('disabled', true);
                } else {
                    spinner.addClass('d-none');
                    btnText.removeClass('d-none');
                    button.prop('disabled', false);
                }
            }

            function updateButtonStates(status) {
                switch (status) {
                    case 'checkin':
                        checkInBtn.prop('disabled', false);
                        checkOutBtn.prop('disabled', true);
                        statusMessage.html('<div class="alert alert-info">Ready for check-in</div>');
                        break;
                    case 'checkout':
                        checkInBtn.prop('disabled', true);
                        checkOutBtn.prop('disabled', false);
                        statusMessage.html('<div class="alert alert-warning">Ready for check-out</div>');
                        break;
                    case 'completed':
                        checkInBtn.prop('disabled', true);
                        checkOutBtn.prop('disabled', true);
                        statusMessage.html(
                            '<div class="alert alert-success">Attendance completed for selected date</div>');
                        break;
                    default:
                        checkInBtn.prop('disabled', true);
                        checkOutBtn.prop('disabled', true);
                        statusMessage.html('');
                }
            }

            function checkAttendanceStatus() {
                const employeeId = employeeSelect.val();
                const date = attendanceDate.val();

                if (!employeeId || !date) {
                    updateButtonStates('none');
                    return;
                }

                $.ajax({
                    url: '{{ route('attendance.check-status') }}',
                    method: 'POST',
                    data: {
                        employee_id: employeeId,
                        date: date,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        updateButtonStates(response.status);
                    },
                    error: function() {
                        statusMessage.html(
                            '<div class="alert alert-danger">Error checking status</div>');
                        updateButtonStates('none');
                    }
                });
            }

            // Check status when either employee or date changes
            employeeSelect.on('change', checkAttendanceStatus);
            attendanceDate.on('change', checkAttendanceStatus);

            function handleAttendance(type) {
                const employeeId = employeeSelect.val();
                const date = attendanceDate.val();
                const button = type === 'checkin' ? checkInBtn : checkOutBtn;

                setLoading(button, true);

                $.ajax({
                    url: '{{ route('attendance.store') }}',
                    method: 'POST',
                    data: {
                        employee_id: employeeId,
                        attendance_type: type,
                        date: date,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const newRow = `
                            <tr>
                                <td>${response.data.employee_name}</td>
                                <td>${response.data.datetime}</td>
                                <td>${response.data.type}</td>
                            </tr>
                        `;
                        attendanceList.prepend(newRow);
                        statusMessage.html(
                        `<div class="alert alert-success">${response.message}</div>`);
                        checkAttendanceStatus();
                        setLoading(button, false);
                    },
                    error: function(xhr) {
                        statusMessage.html(
                            `<div class="alert alert-danger">${xhr.responseJSON?.message || 'Error recording attendance'}</div>`
                            );
                        setLoading(button, false);
                    }
                });
            }

            checkInBtn.on('click', function() {
                handleAttendance('checkin');
            });

            checkOutBtn.on('click', function() {
                handleAttendance('checkout');
            });
        });
    </script>
@endsection

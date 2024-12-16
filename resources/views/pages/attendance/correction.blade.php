@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Attendance Time Correction</h4>
            </div>
            <div class="card-body">
                <form id="correctionForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="employee_id">Select Employee</label>
                        <select class="form-control" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="date">Select Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="type">Select Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="checkin">Check In</option>
                            <option value="checkout">Check Out</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="timeCorrection" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="old_time">Current Time</label>
                                <input type="time" class="form-control" id="old_time" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="new_time">New Time</label>
                                <input type="time" class="form-control" id="new_time" name="new_time" required>
                            </div>
                        </div>
                        <input type="hidden" id="attendance_id" name="attendance_id">
                    </div>

                    <button type="button" class="btn btn-primary mt-3" id="fetchEntry">Fetch Entry</button>
                    <button type="button" class="btn btn-success mt-3" id="updateTime" style="display: none;">Update
                        Time</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Function to reset time correction fields
            function resetTimeCorrection() {
                $('#timeCorrection').hide();
                $('#updateTime').hide();
                $('#old_time').val('');
                $('#new_time').val('');
                $('#attendance_id').val('');
            }

            $('#type').change(function() {
                resetTimeCorrection();
            });

            $('#employee_id').change(function() {
                resetTimeCorrection();
            });

            $('#date').change(function() {
                resetTimeCorrection();
            });

            $('#fetchEntry').click(function() {
                let formData = {
                    employee_id: $('#employee_id').val(),
                    date: $('#date').val(),
                    type: $('#type').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '{{ route('attendance.getEntries') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#timeCorrection').show();
                            $('#updateTime').show();
                            $('#attendance_id').val(response.entry.id);
                            $('#old_time').val(response.current_time);
                            $('#new_time').val(response
                                .current_time);
                        } else {
                            alert(response.message);
                            resetTimeCorrection();
                        }
                    },
                    error: function() {
                        alert('Error fetching attendance entry');
                        resetTimeCorrection();
                    }
                });
            });

            $('#updateTime').click(function() {
                let oldTime = $('#old_time').val();
                let newTime = $('#new_time').val();

                if (oldTime === newTime) {
                    alert('Please enter a different time for correction');
                    return;
                }

                let formData = {
                    attendance_id: $('#attendance_id').val(),
                    new_time: $('#new_time').val(),
                    date: $('#date').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '{{ route('attendance.update') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Time updated successfully');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error updating attendance time');
                    }
                });
            });
        });
    </script>
@endsection

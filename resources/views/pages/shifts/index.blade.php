@extends('layouts.app')
@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header row align-items-center justify-content-between">
                  <div class="col-10">
                      <h3 class="card-title">Shifts</h3>
                  </div>
                  <div class="col-2 text-right">
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createShiftModal">
                          <i class="fas fa-plus"></i> Add Shift
                      </button>
                  </div>
              </div>
                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Overtime Limit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
              </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Create Shift Modal -->
  <div class="modal fade" id="createShiftModal" tabindex="-1" role="dialog" aria-labelledby="createShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createShiftModalLabel">Create New Shift</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="createShiftForm">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Shift Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter shift name" required>
              <span class="text-danger error-text name_error"></span>
            </div>
            <div class="form-group">
              <label for="start_time">Start Time <span class="text-danger">*</span></label>
              <input type="time" class="form-control" id="start_time" name="start_time" required>
              <span class="text-danger error-text start_time_error"></span>
            </div>
            <div class="form-group">
              <label for="end_time">End Time <span class="text-danger">*</span></label>
              <input type="time" class="form-control" id="end_time" name="end_time" required>
              <span class="text-danger error-text end_time_error"></span>
            </div>
            <div class="form-group">
              <label for="overtime_limit">Overtime Limit (mins) <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="overtime_limit" name="overtime_limit" placeholder="Enter overtime limit" step="0.5" min="0" required>
              <span class="text-danger error-text overtime_limit_error"></span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="saveShiftBtn">Save Shift</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
      var dataTable = $('#table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('shifts.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'start_time', name: 'start_time' },
              { data: 'end_time', name: 'end_time' },
              { data: 'overtime_limit', name: 'overtime_limit' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Handle form submission
      $('#createShiftForm').on('submit', function(e) {
          e.preventDefault();
          
          // Clear previous errors
          $('.error-text').text('');
          $('.form-control').removeClass('is-invalid');
          
          // Disable submit button
          $('#saveShiftBtn').prop('disabled', true).text('Saving...');
          
          $.ajax({
              url: "{{ route('shifts.store') }}",
              type: 'POST',
              data: $(this).serialize(),
              success: function(response) {
                  // Close modal
                  $('#closeModal').click();
                  
                  // Reset form
                  $('#createShiftForm')[0].reset();
                  
                  // Reload DataTable
                  dataTable.ajax.reload();
                  
                  // Show success message (you can use toastr or any notification library)
                  alert(response.message);
                  
                  // Re-enable submit button
                  $('#saveShiftBtn').prop('disabled', false).text('Save Shift');
                  
              },
              error: function(xhr) {
                  // Re-enable submit button
                  $('#saveShiftBtn').prop('disabled', false).text('Save Shift');
                  
                  if (xhr.status === 422) {
                      // Validation errors
                      var errors = xhr.responseJSON.errors;
                      $.each(errors, function(key, value) {
                          $('.' + key + '_error').text(value[0]);
                          $('#' + key).addClass('is-invalid');
                      });
                  } else {
                      // Other errors
                      alert('An error occurred: ' + (xhr.responseJSON.message || 'Please try again'));
                  }
              }
          });
      });

      // Clear errors when modal is closed
      $('#createShiftModal').on('hidden.bs.modal', function () {
          $('#createShiftForm')[0].reset();
          $('.error-text').text('');
          $('.form-control').removeClass('is-invalid');
          $('#saveShiftBtn').prop('disabled', false).text('Save Shift');
      });
  });
</script>
@endsection
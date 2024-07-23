@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Branches</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
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

@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
      // DataTable initialization
      var dataTable = $('#table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('branches.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'contact_number', name: 'contact_number' },
              { data: 'address', name: 'address' },
              { data: 'email', name: 'email' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Delete event handler
      $('#table').on('click', '.delete', function(event) {
          event.preventDefault();

          var branchId = $(this).data('id');
          var row = $(this).closest('tr');

          if (confirm("Are you sure you want to delete this branch?")) {
              $.ajax({
                  url: '/branches/' + branchId,
                  type: 'GET', // Use DELETE method for deletion
                  success: function(response) {
                      alert('Branch deleted successfully');
                      dataTable.row(row).remove().draw(false); // Remove row from DataTable
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      alert('Failed to delete branch');
                  }
              });
          }
      });
  });
</script>

@endsection
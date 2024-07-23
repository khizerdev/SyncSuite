@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Roles</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
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
          ajax: "{{ route('roless.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Delete event handler
      $('#table').on('click', '.delete', function(event) {
          event.preventDefault();

          var rolesId = $(this).data('id');
          var row = $(this).closest('tr');

          if (confirm("Are you sure you want to delete this roles?")) {
              $.ajax({
                  url: '/roless/' + rolesId,
                  type: 'GET', // Use DELETE method for deletion
                  success: function(response) {
                      alert('Role deleted successfully');
                      dataTable.row(row).remove().draw(false); // Remove row from DataTable
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      alert('Failed to delete roles');
                  }
              });
          }
      });
  });
</script>

@endsection
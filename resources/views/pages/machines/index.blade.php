@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Machines</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Code</th>
                                <th>Manufacturer</th>
                                <th>Name</th>
                                <th>Production Speed</th>
                                <th>Price</th>
                                <th>Warranty</th>
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
          ajax: "{{ route('machines.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'department', name: 'department' },
              { data: 'code', name: 'code' },
              { data: 'manufacturer', name: 'manufacturer' },
              { data: 'name', name: 'name' },
              { data: 'production_speed', name: 'production_speed' },
              { data: 'price', name: 'price' },
              { data: 'warranty', name: 'warranty' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Delete event handler
      $('#table').on('click', '.delete', function(event) {
          event.preventDefault();

          var machineId = $(this).data('id');
          var row = $(this).closest('tr');

          if (confirm("Are you sure you want to delete this machine?")) {
              $.ajax({
                  url: '/machines/' + machineId,
                  type: 'GET', // Use DELETE method for deletion
                  success: function(response) {
                      alert('Machine deleted successfully');
                      dataTable.row(row).remove().draw(false); // Remove row from DataTable
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      alert('Failed to delete machine');
                  }
              });
          }
      });
  });
</script>

@endsection
@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Vendors</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Telephone</th>
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
          ajax: "{{ route('vendors.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'email', name: 'email' },
              { data: 'address', name: 'address' },
              { data: 'country', name: 'country' },
              { data: 'city', name: 'city' },
              { data: 'telephone', name: 'telephone' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Delete event handler
      $('#table').on('click', '.delete', function(event) {
          event.preventDefault();

          var vendorId = $(this).data('id');
          var row = $(this).closest('tr');

          if (confirm("Are you sure you want to delete this vendor?")) {
              $.ajax({
                  url: '/vendors/' + vendorId,
                  type: 'GET', // Use DELETE method for deletion
                  success: function(response) {
                      alert('Vendor deleted successfully');
                      dataTable.row(row).remove().draw(false); // Remove row from DataTable
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      alert('Failed to delete vendor');
                  }
              });
          }
      });
  });
</script>

@endsection
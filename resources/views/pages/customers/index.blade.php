@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Customers</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a class="btn btn-primary" href="{{route('customers.create')}}">Add New Customer</a>
                </div>
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

  function deleteRecord(id) {
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                  url: '/customers/' + id,
                  type: 'GET',
                  success: function(response) {
                      alert('Customer deleted successfully');
                      dataTable.row(row).remove().draw(false);
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                  }
            });
        }
    });
  }

  $(document).ready(function() {
      var dataTable = $('#table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('customers.index') }}",
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
  });
</script>

@endsection
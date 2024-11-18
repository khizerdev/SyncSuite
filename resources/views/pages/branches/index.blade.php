@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Branches</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a class="btn btn-primary" href="{{route('branches.create')}}">Add New Branch</a>
                </div>
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

  function deleteRecord(id) {
    const baseUrl = "{{env('APP_URL')}}"
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
              url: baseUrl + '/branches/' + id,
              type: 'GET',
              data: {
                  "_token": "{{ csrf_token() }}",
              },
              success: function(response) {
                  Swal.fire(
                      'Deleted!',
                      'The branch has been deleted.',
                      'success'
                  );
                  $('#table').DataTable().ajax.reload();
              },
              error: function(xhr) {
                  const message = xhr.responseJSON.error ? xhr.responseJSON.error : 'There was an error while deleting'
                  Swal.fire(
                      'Error!',
                      message,
                      'error'
                  );
              }
          });
      }
  });
  }

  $(document).ready(function() {
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

  });
</script>

@endsection
@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Receive Supply</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a class="btn btn-primary" href="{{route('supply-receipts.create')}}">Add New Recieve</a>
                </div>
            </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                            <th>Received Date</th>
                            <th>Serial No</th>
                            <th>Department</th>
                            <th>Notes</th>
                            <th>Created At</th>
                            <th>Updated At</th>
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
              url: baseUrl + '/supply-receipts/' + id,
              type: 'GET',
              data: {
                  "_token": "{{ csrf_token() }}",
              },
              success: function(response) {
                  Swal.fire(
                      'Deleted!',
                      'The data has been deleted.',
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
        ajax: "{{ route('supply-receipts.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'received_date', 
                name: 'received_date',
                searchable: false
            },
            { 
                data: 'serial_no', 
                name: 'than_supply.serial_no',
                orderable: false
            },
            { 
                data: 'department.name', 
                name: 'department.name',
                defaultContent: 'N/A'
            },
            { 
                data: 'notes', 
                name: 'notes',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                searchable: false
            },
            { 
                data: 'updated_at', 
                name: 'updated_at',
                searchable: false
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
    });
});
</script>

@endsection
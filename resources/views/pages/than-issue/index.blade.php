@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Than Create</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a class="btn btn-primary" href="{{route('than-issues.create')}}">Add New Than Create</a>
                </div>
            </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issue Date</th>
                                <th>Serial No</th>
                                <th>Remarks</th>
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
              url: baseUrl + '/than-issues/' + id,
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
        ajax: "{{ route('than-issues.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'issue_date', 
                name: 'issue_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-GB');
                }
            },
            { 
                data: 'serial_no', 
                name: 'serial_no',
            },
            { 
                data: 'remarks', 
                name: 'remarks',
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
            }
        ]
    });
});
</script>

@endsection
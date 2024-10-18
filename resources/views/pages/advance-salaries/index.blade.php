@extends('layouts.app')

@section('content')
    <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Advance Salary</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a class="btn btn-primary" href="{{route('advance-salaries.create')}}">Add New Advance</a>
                </div>
            </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
                                <th>Amount</th>
                                {{-- <th>Months</th> --}}
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
              url: '/advance-salaries/' + id,
              type: 'DELETE',
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
                  Swal.fire(
                      'Error!',
                      'There was an error deleting the branch.',
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
          ajax: "{{ route('advance-salaries.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'employee_name', name: 'employee' },
              { data: 'amount', name: 'amount' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

  });
</script>

@endsection
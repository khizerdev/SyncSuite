@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Batches</h3>
                </div>
                <div class="col-6 text-right">
                    
                    <a href="{{ route('batches.create') }}" class="btn btn-primary mb-3">Create New Batch</a>
                </div>
            </div>

                <div class="card-body">
                    <table class="table table-striped" id="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Reference Number</th>
                <th>Department</th>
                <th>Thans</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
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
                        url: "{{ route('batches.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'The batch has been deleted.',
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
          ajax: "{{ route('batches.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'reference_number', name: 'reference_number' },
              { data: 'department', name: 'department' },
              { data: 'items', name: 'items', orderable: false, searchable: true },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

  });
</script>

@endsection
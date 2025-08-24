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
                    <table class="table table-striped">
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Department</th>
                <th>Number of Items</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
                <tr>
                    <td>{{ $batch->reference_number }}</td>
                    <td>{{ $batch->department->name }}</td>
                    <td>{{ $batch->batchItems->count() }}</td>
                    <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <!--<a href="#" class="btn btn-sm btn-info">View</a>-->
                        <!--<a href="#" class="btn btn-sm btn-warning">Edit</a>-->
                        <form action="#" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No batches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
                </div>

              </div>

        </div>
      </div>

    </div>
  </section>

@endsection

@section('scripts')

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
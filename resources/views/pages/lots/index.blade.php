@extends('layouts.app')



@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">
                                <h3 class="card-title">Lot</h3>
                            </div>
                            <div class="col-6 text-right">
                                <a class="btn btn-primary" href="{{ route('lots.create') }}">Add New Lot</a>
                            </div>
                        </div>

                        <div class="card-body">
                            @if($lots->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Batch</th>
                                        <th>Shift</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Run Time (min)</th>
                                        <th>Products Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lots as $lot)
                                        <tr>
                                            <td>{{ $lot->id }}</td>
                                            <td>{{ $lot->batch->reference_number }}</td>
                                            <td>{{ $lot->shift->name }}</td>
                                            <td>{{ $lot->start_time->format('M d, Y H:i') }}</td>
                                            <td>{{ $lot->end_time->format('M d, Y H:i') }}</td>
                                            <td>{{ $lot->run_time }}</td>
                                            <td>{{ $lot->products->count() }}</td>
                                            <td>
                                                <!--<a href="{{ route('lots.show', $lot->id) }}" class="btn btn-info btn-sm">View</a>-->
                                                <!--<a href="{{ route('lots.edit', $lot->id) }}" class="btn btn-warning btn-sm">Edit</a>-->
                                                <form action="{{ route('lots.destroy', $lot->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Are you sure you want to delete this lot?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        
                    @else
                        
                            No lots found. 
                        
                    @endif
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
                        url: "{{ route('loans.destroy', ':id') }}".replace(':id', id),
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
            // DataTable initialization
            var dataTable = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('loans.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });
    </script>
@endsection
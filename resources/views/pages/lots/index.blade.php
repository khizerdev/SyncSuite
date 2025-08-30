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
                            
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Batch</th>
                                        <th>Shift</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Run Time (min)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                
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
            const baseUrl = "{{ env('APP_URL') }}"
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
                        url: "{{ route('lots.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                '',
                                'success'
                            );
                            $('#table').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON.error ? xhr.responseJSON.error :
                                'There was an error while deleting'
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
    </script>

    <script>
        $(document).ready(function() {

            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('lots.index') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'batch.reference_number',
                        name: 'batch'
                    },
                    {
                        data: 'shift.name',
                        name: 'shift'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'run_time',
                        name: 'run_time'
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
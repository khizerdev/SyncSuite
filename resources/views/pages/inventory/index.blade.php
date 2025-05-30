@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <x-content-header title="Inventory" />

        <!--  -->
        <div class="card">
            <div class="card-body">
                <div class=" pt-1 pb-3 container-fluid">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="card-title">All Inventory</h4>
                        </div>
                        <div class="col-6 text-right ">
                            <a class="btn btn-primary" href="{{ route('inventory.bulk_transfer') }}">Bulk Transfer</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Total Received</th>
                                <th>Current Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <!--  -->

    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_in',
                        name: 'total_in'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

            });
        });

        //   const baseUrl = window.location.protocol + "//" + window.location.host; 

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
                        url: `${baseUrl}/inward-general/${id}`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Data has been deleted.',
                                'success'
                            );
                            $('.data-table').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was an error while deleting',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection

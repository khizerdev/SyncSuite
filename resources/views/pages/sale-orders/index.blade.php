@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">

                                <h3 class="card-title">Sale Orders</h3>
                            </div>
                            <div class="col-6 text-right">

                                <a class="btn btn-primary" href="{{ route('sale-orders.create') }}">Create New Sale Order</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="data-table table mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Order Status</th>
                                        <th>Order Reference</th>
                                        <th>Advance Payment</th>
                                        <th>Delivery Date</th>
                                        <th>Action</th>
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
                        url: "{{ route('sale-orders.destroy', ':id') }}".replace(':id', id),
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
                            $('.data-table').DataTable().ajax.reload();
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

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sale-orders.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        data: 'order_status',
                        name: 'order_status'
                    },
                    {
                        data: 'order_reference',
                        name: 'order_reference'
                    },
                    {
                        data: 'advance_payment',
                        name: 'advance_payment'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
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

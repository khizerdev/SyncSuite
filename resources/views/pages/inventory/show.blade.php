@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Product Inventory: {{ $product->name }}</h2>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Inventory Ledger</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="ledger-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Reference</th>
                                    <th>Serial No</th>
                                    <th>IN</th>
                                    <th>OUT</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Department Inventory</h4>

                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="department-inventory-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Ledger DataTable
            $('#ledger-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.show', $product->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'date_formatted',
                        name: 'date'
                    },
                    {
                        data: 'type_badge',
                        name: 'type'
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'serial_no',
                        name: 'serial_no'
                    },
                    {
                        data: 'in',
                        name: 'in'
                    },
                    {
                        data: 'out',
                        name: 'out'
                    },
                    {
                        data: 'balance',
                        name: 'balance'
                    }
                ]
            });

            // Department Inventory DataTable
            $('#department-inventory-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory.show', $product->id) }}",
                    data: {
                        department_inventory: true
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    }
                ]
            });
        });
    </script>
@endsection

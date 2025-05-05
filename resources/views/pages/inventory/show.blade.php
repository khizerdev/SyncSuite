@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Inventory Ledger: {{ $product->name }}</h3>
                    <div>
                        <span class="badge bg-primary">Code: {{ $product->code }}</span>
                        <span class="badge bg-secondary ml-2">Current Stock:
                            {{ $product->purchaseOrderItems->sum('qty') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="ledgerTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Serial No</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="5" class="text-end">Totals:</th>
                            <th id="totalIn"></th>
                            <th id="totalOut"></th>
                            <th id="finalBalance"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inventory
                </a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#ledgerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.show', $product->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date_formatted',
                        name: 'date'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        orderable: false,
                        searchable: false
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
                        name: 'in',
                        className: 'text-success'
                    },
                    {
                        data: 'out',
                        name: 'out',
                        className: 'text-danger'
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        className: 'text-primary'
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Calculate totals
                    var totalIn = api.column(5).data().reduce(function(a, b) {
                        return a + parseFloat(b);
                    }, 0);

                    var totalOut = api.column(6).data().reduce(function(a, b) {
                        return a + parseFloat(b);
                    }, 0);

                    var finalBalance = totalIn - totalOut;

                    // Update footer
                    $('#totalIn').html(totalIn);
                    $('#totalOut').html(totalOut);
                    $('#finalBalance').html(finalBalance);
                }
            });
        });
    </script>
@endsection

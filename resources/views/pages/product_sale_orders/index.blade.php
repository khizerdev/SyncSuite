@extends('layouts.app')

@section('title', 'Product Sale Orders')
@section('css')
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Product Sale Orders</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Product Sale Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="bg-white container-fluid">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title">All Product Sale Orders</h4>
                                </div>
                                <div class="col-6 text-right">
                                    <a class="btn btn-primary" href="{{ route('product_sale_orders.create') }}">Create New</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="saleOrdersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Serial No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product_sale_orders as $order)
                                        <tr>
                                            <td>{{ $order->serial_no }}</td>
                                            <td>{{ $order->date->format('d M Y') }}</td>
                                            <td>{{ $order->customer->name }}</td>
                                            <td>
                                                @php
                                                    $total = 0;
                                                    foreach($order->items as $item) {
                                                        $total += $item->qty * $item->rate;
                                                    }
                                                    echo number_format($total, 2);
                                                @endphp
                                            </td>
                                            <td>
                                                @if($order->payment_method == 'cash')
                                                    <span class="badge badge-success">Cash</span>
                                                @else
                                                    <span class="badge badge-warning">Loan</span>
                                                @endif
                                            </td>
                                           
                                            <td class="action-buttons">
                                                <a href="{{ route('product_sale_orders.show', $order->id) }}" class="btn btn-info btn-sm">View</a>
                                                <a href="{{ route('product_sale_orders.edit', $order->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                                <form action="{{ route('product_sale_orders.destroy', $order->id) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#saleOrdersTable').DataTable({
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [6] }
                ]
            });
        });
    </script>
@endsection
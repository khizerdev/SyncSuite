@extends('layouts.app')

@section('title', 'Receive Loans')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Receive Loans</h4>
                    <div class="page-title-right">
                        <a class="btn btn-primary" href="{{ route('receive_loans.create') }}">
                            <i class="fa fa-plus"></i> New Loan Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                    

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Sale Order No</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receiveLoans as $receiveLoan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $receiveLoan->date }}</td>
                                            <td>{{ $receiveLoan->productSaleOrder->serial_no }}</td>
                                            <td>{{ $receiveLoan->productSaleOrder->customer->name }}</td>
                                            <td>{{ $receiveLoan->items->count() }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-toggle="modal" data-target="#detailsModal{{ $receiveLoan->id }}">
                                                    View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-toggle="modal" data-target="#deleteModal{{ $receiveLoan->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>

                                  
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                No loan receipts found.
                                                <a href="{{ route('receive_loans.create') }}" class="btn btn-primary btn-sm ml-2">
                                                    Create First
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($receiveLoans->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $receiveLoans->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
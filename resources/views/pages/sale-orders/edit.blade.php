@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">Branch</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/branches') }}">View List</a>
                        </li> --}}
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Sale Order</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('sale-orders.update', $saleOrder->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-control" required>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ $saleOrder->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="order_status">Order Status</label>
                                    <select name="order_status" id="order_status" class="form-control" required>
                                        <option value="open" {{ $saleOrder->order_status == 'open' ? 'selected' : '' }}>
                                            Open</option>
                                        <option value="hold" {{ $saleOrder->order_status == 'hold' ? 'selected' : '' }}>
                                            Hold</option>
                                        <option value="cleared"
                                            {{ $saleOrder->order_status == 'cleared' ? 'selected' : '' }}>Cleared</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="order_reference">Order Reference</label>
                                    <input type="text" name="order_reference" id="order_reference" class="form-control"
                                        value="{{ $saleOrder->order_reference }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="advance_payment">Advance Payment</label>
                                    <input type="number" step="0.01" name="advance_payment" id="advance_payment"
                                        class="form-control" value="{{ $saleOrder->advance_payment }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="delivery_date">Delivery Date</label>
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                        value="{{ $saleOrder->delivery_date }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="payment_terms">Payment Terms</label>
                                    <textarea name="payment_terms" id="payment_terms" class="form-control">{{ $saleOrder->payment_terms }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control">{{ $saleOrder->description }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {


        });
    </script>
@endsection

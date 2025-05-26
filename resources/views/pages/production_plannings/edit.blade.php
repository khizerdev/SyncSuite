@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Production Planning</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('production-plannings.update', $productionPlanning->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date" class="form-control" placeholder="Date"
                                                value="{{ old('date', $productionPlanning->date) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Machine Number:</strong>
                                            <select required class="form-control" name="machine_id">
                                                <option value="" disabled selected>Select Machine</option>
                                                @foreach (\App\Models\Machine::all() as $machine)
                                                    <option value="{{ $machine->id }}"
                                                        {{ $productionPlanning->machine_id == $machine->id ? 'selected' : '' }}>
                                                        {{ $machine->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="machine_number" class="form-control"
                                                placeholder="Machine Number"
                                                value="{{ old('machine_number', $productionPlanning->machine_number) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Search Sale Order:</strong>
                                            <input type="text" name="saleorder_search" id="saleorder_search"
                                                class="form-control"
                                                placeholder="Enter Sale Order ID or Fabric Design Code">
                                            <div id="saleorder_search_results" class="mt-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Selected Sale Order:</strong>
                                            <div id="selected_saleorder" class="p-2 rounded">
                                                @if ($productionPlanning->saleOrder)
                                                    <div class="card mb-3">
                                                        <div class="card-header">
                                                            <h5 class="mb-0">Order
                                                                #{{ $productionPlanning->saleOrder->id }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <p class="mb-1"><strong>Customer:</strong>
                                                                        {{ $productionPlanning->saleOrder->customer->name ?? 'N/A' }}
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <p class="mb-1"><strong>Delivery Date:</strong>
                                                                        {{ $productionPlanning->saleOrder->delivery_date ?? 'N/A' }}
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <p class="mb-1"><strong>Status:</strong>
                                                                        {{ $productionPlanning->saleOrder->order_status ?? 'N/A' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">Order Items</h6>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th>Design Code</th>
                                                                            <th>Color</th>
                                                                            <th>Qty</th>
                                                                            <th>Rate</th>
                                                                            <th>Amount</th>
                                                                            <th>Stitch</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($productionPlanning->saleOrder->items as $item)
                                                                            <tr>
                                                                                <td>{{ $item->design->design_code }}</td>
                                                                                <td>{{ $item->color->title }}</td>
                                                                                <td>{{ $item->qty }}</td>
                                                                                <td>{{ $item->rate }}</td>
                                                                                <td>{{ $item->amount }}</td>
                                                                                <td>{{ $item->stitch }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="hidden" name="saleorder_id" id="saleorder_id"
                                                value="{{ old('saleorder_id', $productionPlanning->sale_order_id) }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ route('production-plannings.index') }}"
                                            class="btn btn-default">Cancel</a>
                                    </div>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('saleorder_search');
            const resultsDiv = document.getElementById('saleorder_search_results');
            const selectedDiv = document.getElementById('selected_saleorder');
            const saleorderIdInput = document.getElementById('saleorder_id');

            // Debounce function to limit API calls
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const searchTerm = this.value.trim();

                debounceTimer = setTimeout(() => {
                    fetchSaleOrders(searchTerm);
                }, 300);
            });

            function fetchSaleOrders(searchTerm) {
                const baseUrl = "{{ env('APP_URL') }}"
                fetch(`${baseUrl}/api/sale-orders/search?q=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            function displayResults(saleorders) {
                if (saleorders.length === 0) {
                    resultsDiv.innerHTML = '<div class="text-muted">No sale orders found</div>';
                    return;
                }

                let html = '<ul class="list-group">';
                saleorders.forEach(order => {
                    html += `
                    <li class="list-group-item list-group-item-action" 
                        data-id="${order.id}"
                        style="cursor: pointer;">
                        <strong>Order #${order.id}</strong><br>
                        Customer: ${order.customer.name}<br>
                        Items: ${order.items_count}<br>
                        Delivery Date: ${order.delivery_date}<br>
                        Status: ${order.order_status}
                    </li>
                `;
                });
                html += '</ul>';

                resultsDiv.innerHTML = html;

                // Add click event to results
                document.querySelectorAll('#saleorder_search_results li').forEach(item => {
                    item.addEventListener('click', function() {
                        const orderId = this.getAttribute('data-id');
                        const order = saleorders.find(o => o.id == orderId);
                        selectSaleOrder(order);
                    });
                });
            }

            function selectSaleOrder(order) {
                // Create the order header info
                let html = `
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Order #${order.id}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Customer:</strong> ${order.customer.name || 'N/A'}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Delivery Date:</strong> ${order.delivery_date || 'N/A'}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Status:</strong> ${order.order_status || 'N/A'}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Order Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Design Code</th>
                                <th>Color</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Stitch</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

                // Add each item to the table
                order.items.forEach(item => {
                    html += `
            <tr>
                <td>${item.design.design_code}</td>
                <td>${item.color.title}</td>
                <td>${item.qty}</td>
                <td>${item.rate}</td>
                <td>${item.amount}</td>
                <td>${item.stitch}</td>
            </tr>
        `;
                });

                // Close the table and cards
                html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

                selectedDiv.innerHTML = html;
                saleorderIdInput.value = order.id;
                resultsDiv.innerHTML = '';
                searchInput.value = '';
            }
        });
    </script>
@endsection

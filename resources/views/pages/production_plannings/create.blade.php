@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Production Planning</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('production-plannings.store') }}" method="POST" id="productionPlanningForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date" class="form-control" placeholder="Date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <strong>Machine Number:</strong>
                                            <select required class="form-control" name="machine_id" required>
                                                <option value="" disabled selected>Select Machine</option>
                                                @foreach (\App\Models\Machine::all() as $machine)
                                                    <option value="{{ $machine->id }}">{{ $machine->code }}</option>
                                                @endforeach
                                            </select>
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
                                            <strong>Select Item for Production:</strong>
                                            <div id="selected_saleorder" class="p-2 rounded"></div>
                                            <input type="hidden" name="sale_order_id" id="sale_order_id">
                                            <input type="hidden" name="sale_order_item_id" id="sale_order_item_id">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
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
        const assetBase = "{{ asset('') }}";

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('saleorder_search');
            const resultsDiv = document.getElementById('saleorder_search_results');
            const selectedDiv = document.getElementById('selected_saleorder');
            const saleOrderIdInput = document.getElementById('sale_order_id');
            const saleOrderItemIdInput = document.getElementById('sale_order_item_id');
            const form = document.getElementById('productionPlanningForm');

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
                            <h6 class="mb-0">Select Item for Production</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Select</th>
                                            <th>Design Code</th>
                                            <th>Color</th>
                                            <th>Stitch</th>
                                            <th>Original Lace Qty</th>
                                            <th>Planned Lace Qty</th>
                                            <th>Original Qty</th>
                                            <th>Planned Qty</th>
                                            <th>Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                // Add each item to the table with editable fields
                order.items.forEach(item => {
                    html += `
                        <tr>
                            <td>
                                <input type="radio" name="selected_item" 
                                    value="${item.id}" 
                                    data-order-id="${order.id}"
                                    class="item-radio"
                                    required>
                            </td>
                            <td>${item.design.design_code}</td>
                            <td>${item.color.title}</td>
                            <td>${item.stitch}</td>
                            <td>${item.lace_qty}</td>
                            <td>
                                <input type="number" 
                                    name="items[${item.id}][planned_lace_qty]" 
                                    value="${item.lace_qty}" 
                                    min="0" max="${item.lace_qty}"
                                    class="form-control form-control-sm planned-lace-qty"
                                    disabled
                                    required>
                            </td>
                            <td>${item.qty}</td>
                            <td>
                                <input type="number" 
                                    name="items[${item.id}][planned_qty]" 
                                    value="${item.qty}" 
                                    min="1" max="${item.qty}"
                                    class="form-control form-control-sm planned-qty"
                                    disabled
                                    required>
                            </td>
                            <td><img src="${assetBase}${item.design.design_picture}" width="50" /></td>
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
                saleOrderIdInput.value = '';
                saleOrderItemIdInput.value = '';
                resultsDiv.innerHTML = '';
                searchInput.value = '';

                // Add radio button change event
                document.querySelectorAll('.item-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        // Enable/disable quantity fields based on selection
                        document.querySelectorAll('.planned-qty, .planned-lace-qty').forEach(field => {
                            field.disabled = true;
                        });

                        if (this.checked) {
                            const itemId = this.value;
                            const orderId = this.getAttribute('data-order-id');
                            saleOrderIdInput.value = orderId;
                            saleOrderItemIdInput.value = itemId;
                            
                            // Enable fields for selected item
                            const row = this.closest('tr');
                            row.querySelector('.planned-qty').disabled = false;
                            row.querySelector('.planned-lace-qty').disabled = false;
                            
                            // Set the form values (since we're using array notation in the table)
                            form.elements['planned_qty'] = row.querySelector('.planned-qty');
                            form.elements['planned_lace_qty'] = row.querySelector('.planned-lace-qty');
                        }
                    });
                });
            }
        });
    </script>
@endsection
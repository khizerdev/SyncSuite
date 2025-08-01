@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Daily Production</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('daily-productions.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Shift:</strong>
                                            <select name="shift_id" class="form-control" required>
                                                <option value="">Select Shift</option>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}">{{ $shift->name }}
                                                        ({{ $shift->start_time }} - {{ $shift->end_time }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Machine:</strong>
                                            <select name="machine_id" class="form-control" required>
                                                <option value="">Select Machine</option>
                                                @foreach ($machines as $machine)
                                                    <option value="{{ $machine->id }}">{{ $machine->name }}
                                                        ({{ $machine->model }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Previous Stitch:</strong>
                                            <input type="number" id="previous_stitch" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Current Stitch:</strong>
                                            <input type="number" name="current_stitch" id="current_stitch"
                                                class="form-control" placeholder="Current Stitch" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <strong>Actual Stitch:</strong>
                                            <input type="number" id="actual_stitch" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Description:</strong>
                                            <textarea class="form-control" style="height:150px" name="description" placeholder="Description"></textarea>
                                        </div>
                                    </div>

                                    <!-- Sale Orders Section -->
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Sale Orders</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <strong>Search Sale Order:</strong>
                                                    <input type="text" name="saleorder_search" id="saleorder_search"
                                                        class="form-control"
                                                        placeholder="Enter Sale Order ID or Fabric Design Code">
                                                    <div id="saleorder_search_results" class="mt-2"></div>
                                                    <small id="duplicate-error" class="text-danger d-none">This order is
                                                        already added</small>
                                                </div>

                                                <div id="selected_saleorders">
                                                    <!-- Selected sale orders will appear here -->
                                                </div>
                                            </div>
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
        $(document).ready(function() {
            const baseUrl = "{{ env('APP_URL') }}"
            $('select[name="machine_id"]').change(function() {
                var machineId = $(this).val();
                if (machineId) {
                    $.get(`${baseUrl}/api/get-previous-stitch/` + machineId, function(data) {
                        $('#previous_stitch').val(data.previous_stitch || 0);
                        calculateActualStitch();
                    });
                } else {
                    $('#previous_stitch').val(0);
                    calculateActualStitch();
                }
            });

            // Calculate actual stitch when current stitch changes
            $('#current_stitch').on('input', function() {
                calculateActualStitch();
            });

            function calculateActualStitch() {
                var previous = parseInt($('#previous_stitch').val()) || 0;
                var current = parseInt($('#current_stitch').val()) || 0;
                var actual = current - previous;
                $('#actual_stitch').val(actual);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('saleorder_search');
            const resultsDiv = document.getElementById('saleorder_search_results');
            const selectedDiv = document.getElementById('selected_saleorders');
            const duplicateError = document.getElementById('duplicate-error');
            let selectedOrders = [];

            // Debounce function to limit API calls
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const searchTerm = this.value.trim();
                duplicateError.classList.add('d-none');

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
                    // Check if order is already selected
                    const isSelected = selectedOrders.some(o => o.id == order.id);
                    const selectedClass = isSelected ? 'bg-light text-muted' : '';
                    const selectedText = isSelected ? ' (Already added)' : '';

                    html += `
                    <li class="list-group-item list-group-item-action ${selectedClass}" 
                        data-id="${order.id}"
                        style="cursor: pointer;"
                        ${isSelected ? 'onclick="showDuplicateError()"' : `onclick="addSaleOrder(${order.id}, ${JSON.stringify(order).replace(/"/g, '&quot;')})"`}>
                        <strong>Order #${order.id}${selectedText}</strong><br>
                        Customer: ${order.customer.name}<br>
                        Items: ${order.items_count}<br>
                        Delivery Date: ${order.delivery_date}<br>
                        Status: ${order.order_status}
                    </li>
                `;
                });
                html += '</ul>';

                resultsDiv.innerHTML = html;
            }

            // Global function to be called from onclick
            window.addSaleOrder = function(orderId, order) {
                // Check if order is already selected
                if (selectedOrders.some(o => o.id == orderId)) {
                    showDuplicateError();
                    return;
                }

                selectedOrders.push(order);
                renderSelectedOrders();
                resultsDiv.innerHTML = '';
                searchInput.value = '';
                duplicateError.classList.add('d-none');
            };

            window.showDuplicateError = function() {
                duplicateError.classList.remove('d-none');
                setTimeout(() => {
                    duplicateError.classList.add('d-none');
                }, 3000);
            };

            window.removeSaleOrder = function(orderId) {
                selectedOrders = selectedOrders.filter(order => order.id != orderId);
                renderSelectedOrders();
                // Re-fetch results to update the "already added" status
                if (searchInput.value.trim().length > 1) {
                    fetchSaleOrders(searchInput.value.trim());
                }
            }

            function renderSelectedOrders() {
    let html = '';

    // First, collect all current values before re-rendering
    const currentValues = {};
    document.querySelectorAll('.selected-order').forEach(orderElement => {
        const orderId = orderElement.getAttribute('data-order-id');
        currentValues[orderId] = {};

        orderElement.querySelectorAll('input[name^="saleorders"]').forEach(input => {
            if (input.name.includes('[needle]')) {
                const matches = input.name.match(
                    /saleorders\[(\d+)\]\[items\]\[(\d+)\]\[needle\]/);
                if (matches) {
                    const orderIndex = matches[1];
                    const itemIndex = matches[2];
                    currentValues[orderId][itemIndex] = currentValues[orderId][itemIndex] || {};
                    currentValues[orderId][itemIndex].needle = input.value;
                }
            } else if (input.name.includes('[lace_qty]')) {
                const matches = input.name.match(
                    /saleorders\[(\d+)\]\[items\]\[(\d+)\]\[lace_qty\]/);
                if (matches) {
                    const orderIndex = matches[1];
                    const itemIndex = matches[2];
                    currentValues[orderId][itemIndex] = currentValues[orderId][itemIndex] || {};
                    currentValues[orderId][itemIndex].lace_qty = input.value;
                }
            } else if (input.name.includes('[qty]')) {
                const matches = input.name.match(
                    /saleorders\[(\d+)\]\[items\]\[(\d+)\]\[qty\]/);
                if (matches) {
                    const orderIndex = matches[1];
                    const itemIndex = matches[2];
                    currentValues[orderId][itemIndex] = currentValues[orderId][itemIndex] || {};
                    currentValues[orderId][itemIndex].qty = input.value;
                }
            }
        });
    });

    // Now render each order while preserving values
    selectedOrders.forEach((order, orderIndex) => {
        html += `
<div class="card mb-3 selected-order" data-order-id="${order.id}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order #${order.id}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-order" 
            data-order-id="${order.id}">
            Remove
        </button>
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
        
        <input type="hidden" name="saleorders[${orderIndex}][id]" value="${order.id}">
        
        <div class="table-responsive mt-3">
            <table class="table table-sm table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Design Code</th>
                        <th>Color</th>
                        <th>Lace Qty (Remaining/Total)</th>
                        <th>Than Qty (Remaining/Total)</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Stitch</th>
                        <th>Total Stitch</th>
                        <th width="100px">Needle</th>
                    </tr>
                </thead>
                <tbody>`;

        // Add items for this order
        order.items.forEach((item, itemIndex) => {
            // Get saved values if they exist
            const savedValues = currentValues[order.id]?.[itemIndex] || {};
            const savedNeedle = savedValues.needle || '';
            const savedLaceQty = savedValues.lace_qty !== undefined ? savedValues.lace_qty : 0;
            const savedQty = savedValues.qty !== undefined ? savedValues.qty : 0;

            // Calculate remaining quantities
            const totalLaceQty = item.lace_qty;
            const totalQty = item.qty;
            
            // Get previously used quantities from the database
            // This would need to be passed from the controller initially
            const usedLaceQty = item.used_lace_qty || 0;
            const usedQty = item.used_qty || 0;
            
            const remainingLaceQty = totalLaceQty - usedLaceQty;
            const remainingQty = totalQty - usedQty;

            // Calculate max values for this production
            const maxLaceQty = remainingLaceQty;
            const maxQty = remainingQty;

            html += `
                    <tr>
                        <td>${item.design.design_code}</td>
                        <td>${item.color.title}</td>
                        <td>
                            <div class="input-group">
                                <input type="number" 
                                       name="saleorders[${orderIndex}][items][${itemIndex}][lace_qty]" 
                                       class="form-control lace-qty-input" 
                                       min="0" 
                                       value="${savedLaceQty}"
                                       >
                                <div class="input-group-append">
                                    <span class="input-group-text">${remainingLaceQty}/${totalLaceQty}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" 
                                       name="saleorders[${orderIndex}][items][${itemIndex}][qty]" 
                                       class="form-control qty-input" 
                                       min="0" 
                                       value="${savedQty}"
                                      
                                       >
                                <div class="input-group-append">
                                    <span class="input-group-text">${remainingQty}/${totalQty}</span>
                                </div>
                            </div>
                        </td>
                        <td>${item.rate}</td>
                        <td>${item.amount}</td>
                        <td>${item.stitch}</td>
                        <td>${item.stitch * item.lace_qty}</td>
                        <td>
                            <input type="text" 
                                   name="saleorders[${orderIndex}][items][${itemIndex}][needle]" 
                                   class="form-control" 
                                   placeholder="Needle" 
                                   value="${savedNeedle}"
                                   required>
                            <input type="hidden" 
                                   name="saleorders[${orderIndex}][items][${itemIndex}][sale_order_item_id]" 
                                   value="${item.id}">
                        </td>
                    </tr>`;
        });

        html += `
                </tbody>
            </table>
        </div>
    </div>
</div>`;
    });

    selectedDiv.innerHTML = html;

    // Add event listeners to remove buttons
    selectedDiv.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-order')) {
            const orderId = e.target.getAttribute('data-order-id');
            removeSaleOrder(orderId);
        }
    });

    // Add validation for lace_qty and qty inputs
    // document.querySelectorAll('.lace-qty-input').forEach(input => {
    //     input.addEventListener('change', function() {
    //         const max = parseInt(this.getAttribute('max'));
    //         const value = parseInt(this.value) || 0;
            
    //         if (value > max) {
    //             this.value = max;
    //             alert(`Lace quantity cannot exceed remaining quantity (${max})`);
    //         }
    //     });
    // });

    // document.querySelectorAll('.qty-input').forEach(input => {
    //     input.addEventListener('change', function() {
    //         const max = parseInt(this.getAttribute('max'));
    //         const value = parseInt(this.value) || 0;
            
    //         if (value > max) {
    //             this.value = max;
    //             alert(`Quantity cannot exceed remaining quantity (${max})`);
    //         }
    //     });
    // });
}
        });
    </script>
@endsection
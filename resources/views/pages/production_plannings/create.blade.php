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
                                            <strong>Select Items for Production:</strong>
                                            <div id="selected_saleorder" class="p-2 rounded"></div>
                                            <input type="hidden" name="sale_order_id" id="sale_order_id">
                                            <!-- Hidden inputs for selected items will be added dynamically -->
                                            <div id="selected_items_inputs"></div>
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
            const selectedItemsInputsDiv = document.getElementById('selected_items_inputs');
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Select Items for Production</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllItems">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllItems">Deselect All</button>
                            </div>
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
                        <tr data-item-id="${item.id}">
                            <td>
                                <input type="checkbox" 
                                    name="selected_items[]" 
                                    value="${item.id}" 
                                    data-order-id="${order.id}"
                                    class="item-checkbox">
                            </td>
                            <td>${item.design.design_code}</td>
                            <td>${item.color.title}</td>
                            <td>${item.stitch}</td>
                            <td>${item.lace_qty}</td>
                            <td>
                                <input type="number" 
                                    name="planned_lace_qty_${item.id}" 
                                    value="${item.lace_qty}" 
                                    min="0" max="${item.lace_qty}"
                                    class="form-control form-control-sm planned-lace-qty"
                                    disabled
                                    data-item-id="${item.id}">
                            </td>
                            <td>${item.qty}</td>
                            <td>
                                <input type="number" 
                                    name="planned_qty_${item.id}" 
                                    value="${item.qty}" 
                                    min="1" max="${item.qty}"
                                    class="form-control form-control-sm planned-qty"
                                    disabled
                                    data-item-id="${item.id}">
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
                saleOrderIdInput.value = order.id;
                selectedItemsInputsDiv.innerHTML = '';
                resultsDiv.innerHTML = '';
                searchInput.value = '';

                // Add checkbox change event
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const itemId = this.value;
                        const row = this.closest('tr');
                        const plannedQtyInput = row.querySelector('.planned-qty');
                        const plannedLaceQtyInput = row.querySelector('.planned-lace-qty');
                        
                        if (this.checked) {
                            // Enable fields for selected item
                            plannedQtyInput.disabled = false;
                            plannedLaceQtyInput.disabled = false;
                            plannedQtyInput.required = true;
                            plannedLaceQtyInput.required = true;
                            
                            // Add hidden inputs for form submission
                            addSelectedItemInput(itemId);
                        } else {
                            // Disable fields for deselected item
                            plannedQtyInput.disabled = true;
                            plannedLaceQtyInput.disabled = true;
                            plannedQtyInput.required = false;
                            plannedLaceQtyInput.required = false;
                            
                            // Remove hidden inputs
                            removeSelectedItemInput(itemId);
                        }
                        
                        updateSelectAllButton();
                    });
                });

                // Select All / Deselect All functionality
                document.getElementById('selectAllItems').addEventListener('click', function() {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        if (!checkbox.checked) {
                            checkbox.checked = true;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                });

                document.getElementById('deselectAllItems').addEventListener('click', function() {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        if (checkbox.checked) {
                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                });
            }

            function addSelectedItemInput(itemId) {
                // Create hidden input for the selected item
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_items[]';
                hiddenInput.value = itemId;
                hiddenInput.id = `selected_item_${itemId}`;
                selectedItemsInputsDiv.appendChild(hiddenInput);
            }

            function removeSelectedItemInput(itemId) {
                const hiddenInput = document.getElementById(`selected_item_${itemId}`);
                if (hiddenInput) {
                    hiddenInput.remove();
                }
            }

            function updateSelectAllButton() {
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                const selectAllBtn = document.getElementById('selectAllItems');
                const deselectAllBtn = document.getElementById('deselectAllItems');
                
                if (allCheckboxes.length === checkedCheckboxes.length) {
                    selectAllBtn.textContent = 'All Selected';
                    selectAllBtn.disabled = true;
                    deselectAllBtn.disabled = false;
                } else if (checkedCheckboxes.length === 0) {
                    selectAllBtn.textContent = 'Select All';
                    selectAllBtn.disabled = false;
                    deselectAllBtn.disabled = true;
                } else {
                    selectAllBtn.textContent = 'Select All';
                    selectAllBtn.disabled = false;
                    deselectAllBtn.disabled = false;
                }
            }

            // Form validation before submit
            form.addEventListener('submit', function(e) {
                const checkedItems = document.querySelectorAll('.item-checkbox:checked');
                if (checkedItems.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one item for production.');
                    return false;
                }
                
                // Validate that all selected items have valid quantities
                let hasInvalidQuantity = false;
                checkedItems.forEach(checkbox => {
                    const itemId = checkbox.value;
                    const plannedQty = document.querySelector(`input[name="planned_qty_${itemId}"]`).value;
                    const plannedLaceQty = document.querySelector(`input[name="planned_lace_qty_${itemId}"]`).value;
                    
                    if (!plannedQty || plannedQty <= 0 || !plannedLaceQty || plannedLaceQty < 0) {
                        hasInvalidQuantity = true;
                    }
                });
                
                if (hasInvalidQuantity) {
                    e.preventDefault();
                    alert('Please enter valid quantities for all selected items.');
                    return false;
                }
            });
        });
    </script>
@endsection
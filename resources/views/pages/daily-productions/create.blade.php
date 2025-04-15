@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            < <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Daily Production</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('daily-productions.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
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
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Date:</strong>
                                            <input type="date" name="date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
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
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Previous Stitch:</strong>
                                            <input type="number" id="previous_stitch" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Current Stitch:</strong>
                                            <input type="number" name="current_stitch" id="current_stitch"
                                                class="form-control" placeholder="Current Stitch" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
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
                                            <div id="selected_saleorder" class="p-2  rounded"></div>
                                            <input type="hidden" name="saleorder_id" id="saleorder_id">
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
                var actual = previous - current;
                $('#actual_stitch').val(actual);
            }
        });
    </script>

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

                // if (searchTerm.length < 2) {
                //     resultsDiv.innerHTML = '';
                //     return;
                // }

                debounceTimer = setTimeout(() => {
                    fetchSaleOrders(searchTerm);
                }, 300);
            });

            function fetchSaleOrders(searchTerm) {
                fetch(`/api/sale-orders/search?q=${encodeURIComponent(searchTerm)}`)
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
                            <th>Lace Qty</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Stitch</th>
                            <th>Total Stitch</th>
                            <th wdith="20px">Needle</th>
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
            <td>${item.lace_qty}</td>
            <td>${item.qty}</td>
            <td>${item.rate}</td>
            <td>${item.amount}</td>
            <td>${item.stitch}</td>
            <td>${item.stitch*item.lace_qty}</td>
            <td><input type="text" name="needle[]" class="form-control w-25" placeholder="" required></td>
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

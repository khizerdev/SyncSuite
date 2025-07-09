@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Than Issue Create</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('than-issues.store') }}" method="POST" id="thanIssueForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" 
                                                   value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                    
                                       <div class="col-md-12" id="product_group_id" >
                                        <div class="form-group">
                                            <label for="product_group_id">Product Group</label>
                                            <select class="form-control" id="product_group_id" name="product_group_id" required>
                                                <option value="">Select Product Group</option>
                                                @foreach($productGroups as $productGroup)
                                                    <option value="{{ $productGroup->id }}" {{ old('product_group_id') == $productGroup->id ? 'selected' : '' }}>
                                                        {{ $productGroup->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Daily Productions Section -->
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Daily Productions</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <strong>Search Daily Production:</strong>
                                                    <input type="text" name="production_search" id="production_search"
                                                        class="form-control"
                                                        placeholder="Enter Production ID or Sale Order ID">
                                                    <div id="production_search_results" class="mt-2"></div>
                                                </div>

                                                <div id="selected_productions">
                                                    <!-- Selected items will appear here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                 

                                    <!-- Job Type Section -->
                                    <!--<div class="col-md-6 d-none">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label>Job Type</label>-->
                                    <!--        <div>-->
                                    <!--            <div class="form-check form-check-inline">-->
                                    <!--                <input class="form-check-input" type="radio" name="job_type" id="job_type_department" -->
                                    <!--                       value="department" {{ old('job_type') == 'department' ? 'checked' : '' }} required>-->
                                    <!--                <label class="form-check-label" for="job_type_department">Department</label>-->
                                    <!--            </div>-->
                                    <!--            <div class="form-check form-check-inline">-->
                                    <!--                <input class="form-check-input" type="radio" name="job_type" id="job_type_party" -->
                                    <!--                       value="party" {{ old('job_type') == 'party' ? 'checked' : '' }}>-->
                                    <!--                <label class="form-check-label" for="job_type_party">Party</label>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->

                                    <!--<div class="col-md-6 d-none" id="department_field" -->
                                    <!--     style="{{ old('job_type') == 'department' ? '' : 'display: none;' }}">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label for="department_id">Department</label>-->
                                    <!--        <select class="form-control" id="department_id" name="department_id">-->
                                    <!--            <option value="">Select Department</option>-->
                                    <!--            @foreach($departments as $department)-->
                                    <!--                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>-->
                                    <!--                    {{ $department->name }}-->
                                    <!--                </option>-->
                                    <!--            @endforeach-->
                                    <!--        </select>-->
                                    <!--    </div>-->
                                    <!--</div>-->

                                    <!--<div class="col-md-6 d-none" id="party_field" -->
                                    <!--     style="{{ old('job_type') == 'party' ? '' : 'display: none;' }}">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label for="party_id">Party</label>-->
                                    <!--        <select class="form-control" id="party_id" name="party_id">-->
                                    <!--            <option value="">Select Party</option>-->
                                    <!--            @foreach($parties as $party)-->
                                    <!--                <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>-->
                                    <!--                    {{ $party->name }}-->
                                    <!--                </option>-->
                                    <!--            @endforeach-->
                                    <!--        </select>-->
                                    <!--    </div>-->
                                    <!--</div>-->

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('than-issues.index') }}" class="btn btn-secondary">Cancel</a>
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
        // Toggle department/party fields based on job type selection
        $('input[name="job_type"]').change(function() {
            if ($(this).val() === 'department') {
                $('#department_field').show();
                $('#party_field').hide();
                $('#party_id').val('');
            } else {
                $('#department_field').hide();
                $('#party_field').show();
                $('#department_id').val('');
            }
        });

        // Daily Production Search and Selection
        const searchInput = $('#production_search');
        const resultsDiv = $('#production_search_results');
        const selectedDiv = $('#selected_productions');
        let selectedItems = [];

        // Debounce search
        let debounceTimer;
        searchInput.on('input', function() {
            clearTimeout(debounceTimer);
            const searchTerm = $(this).val().trim();

            if (searchTerm.length > 1) {
                debounceTimer = setTimeout(() => {
                    fetchProductions(searchTerm);
                }, 300);
            } else {
                resultsDiv.empty();
            }
        });
        
        const baseUrl = "{{env('APP_URL')}}"
        function fetchProductions(searchTerm) {
            $.get(`${baseUrl}/api/daily-productions/search?q=${encodeURIComponent(searchTerm)}`, function(data) {
                displayProductionResults(data);
            }).fail(function() {
                resultsDiv.html('<div class="text-danger">Error fetching data</div>');
            });
        }

        function displayProductionResults(productions) {
            let html = '<ul class="list-group">';
            
            productions.forEach(production => {
                html += `
                    <li class="list-group-item">
                        <strong>Production #${production.id}</strong> (${production.date})<br>
                        Machine: ${production.machine?.name || 'N/A'}<br>
                        Shift: ${production.shift?.name || 'N/A'}`;
                
                if (production.items && production.items.length > 0) {
                    html += `<ul class="list-group mt-2">`;
                    
                    production.items.forEach(item => {
                        // Skip if already selected
                        const isSelected = selectedItems.some(i => i.parentId === item.id);
                        const itemClass = isSelected ? 'bg-light text-muted' : '';
                        
                        html += `
                            <li class="list-group-item list-group-item-action ${itemClass}" 
                                data-id="${item.id}"
                                onclick="handleItemClick(${item.id}, '${item.sale_order_id}', ${item.than_qty}, this)">
                                <strong>Sale Order #${item.sale_order_id}</strong><br>
                                Than Qty: ${item.than_qty}
                                ${isSelected ? '<span class="float-right text-success">✓ Selected</span>' : ''}
                            </li>`;
                    });
                    
                    html += `</ul>`;
                }
                
                html += `</li>`;
            });
            
            html += '</ul>';
            resultsDiv.html(html);
        }

        // Global click handler
        window.handleItemClick = function(itemId, saleOrderId, thanQty, element) {
            // Check if already selected (using parentId to track the original item)
            const isSelected = selectedItems.some(item => item.parentId === itemId);
            
            if (isSelected) {
                // Remove all items with this parentId
                selectedItems = selectedItems.filter(item => item.parentId !== itemId);
                $(element).removeClass('bg-light text-muted');
                $(element).find('.float-right').remove();
            } else {
                // Add new items - one for each than_qty
                for (let i = 1; i <= thanQty; i++) {
                    selectedItems.push({
                        id: `${itemId}-${i}`, // Unique ID for each row
                        parentId: itemId,     // Original item ID
                        sale_order_id: saleOrderId,
                        than_qty: thanQty,
                        sequence: i            // Track position (1 to than_qty)
                    });
                }
                $(element).addClass('bg-light text-muted');
                $(element).append('<span class="float-right text-success">✓ Selected</span>');
            }
            
            renderSelectedItems();
        };

        // Render selected items table with one row per than_qty
        function renderSelectedItems() {
            if (selectedItems.length === 0) {
                selectedDiv.html('<div class="alert alert-info">No items selected yet</div>');
                return;
            }

            let html = `
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Sale Order</th>
                                <th>Item Sequence</th>
                                <th>Total Than Qty</th>
                            </tr>
                        </thead>
                        <tbody>`;

            // Sort by parentId and sequence for better display
            selectedItems.sort((a, b) => {
                if (a.parentId === b.parentId) {
                    return a.sequence - b.sequence;
                }
                return a.parentId - b.parentId;
            });

            selectedItems.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.sale_order_id}</td>
                        <td>Item ${item.sequence} of ${item.than_qty}</td>
                        <td>${item.than_qty}
                            <input type="hidden" name="daily_production_item_ids[]" value="${item.parentId}">
                        </td>
                    </tr>`;
            });

            html += `</tbody></table></div>`;
            selectedDiv.html(html);
        }

        // Global remove function for all items with same parentId
        window.removeSelectedItem = function(itemId) {
            selectedItems = selectedItems.filter(item => item.parentId !== itemId);
            renderSelectedItems();
            
            // Update search results to show item as available again
            $(`[data-id="${itemId}"]`).removeClass('bg-light text-muted')
                .find('.float-right').remove();
        };

        // Form submission
        $('#thanIssueForm').on('submit', function(e) {
            if (selectedItems.length === 0) {
                e.preventDefault();
                alert('Please add at least one production item');
            }
        });
    });
</script>

@endsection
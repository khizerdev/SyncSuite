@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Than Edit</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('than-issues.update', $thanIssue->id) }}" method="POST" id="thanIssueForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="{{ old('date', $thanIssue->issue_date) }}" required>
            </div>
        </div>
        
        <div class="col-md-12" id="product_group_id">
            <div class="form-group">
                <label for="product_group_id">Product Group</label>
                <select class="form-control" id="product_group_id" name="product_group_id" required>
                    <option value="">Select Product Group</option>
                    @foreach($productGroups as $productGroup)
                        <option value="{{ $productGroup->id }}" 
                            {{ old('product_group_id', $thanIssue->product_group_id) == $productGroup->id ? 'selected' : '' }}>
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
        
        <div class="col-md-12">
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks">{{ old('remarks', $thanIssue->remarks) }}</textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Update</button>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get designs passed from controller
        const designs = @json($designs ?? []);
        
        // Get existing than issue items
        const existingItems = @json($thanIssueItems ?? []);

        // Initialize Select2 for design dropdowns
        function initSelect2(element) {
            $(element).select2({
                placeholder: "Select Designs",
                allowClear: true,
                data: designs.map(design => ({
                    id: design.id,
                    text: design.design_code,
                    description: design.description
                })),
                templateResult: formatDesign,
                templateSelection: formatDesignSelection,
                escapeMarkup: function (markup) { return markup; }
            });
        }

        function formatDesign(design) {
            if (design.loading) return design.text;
            
            var markup = "<div class='select2-result-design clearfix'>" +
                "<div class='select2-result-design__title'>" + design.text + "</div>";
            
            if (design.description) {
                markup += "<div class='select2-result-design__description'>" + design.description + "</div>";
            }
            
            markup += "</div>";
            return markup;
        }

        function formatDesignSelection(design) {
            return design.text;
        }

        // Daily Production Search and Selection
        const searchInput = $('#production_search');
        const resultsDiv = $('#production_search_results');
        const selectedDiv = $('#selected_productions');
        let selectedItems = [];

        // Initialize with existing items
        function initializeExistingItems() {
            existingItems.forEach((item, index) => {
                selectedItems.push({
                    id: item.id,
                    parentId: item.daily_production_item_id,
                    sale_order_id: item.daily_production_item?.sale_order_item?.sale_order_id || 'N/A',
                    design_name: item.daily_production_item?.design?.design_code || 'N/A',
                    color_name: item.daily_production_item?.color?.title || 'N/A',
                    than_qty: item.daily_production_item?.than_qty || 0,
                    sequence: index + 1,
                    lace_qty: item.lace_qty,
                    weight: item.weight,
                    existing_designs: item.fabric_measurements || []
                });
            });
            renderSelectedItems();
        }

        // Initialize existing items on page load
        initializeExistingItems();

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
                        console.log(item)
                        // Skip if already selected
                        const isSelected = selectedItems.some(i => i.parentId === item.id);
                        const itemClass = isSelected ? 'bg-light text-muted' : '';
                        
                        html += `
                            <li class="list-group-item list-group-item-action ${itemClass}" 
                                data-id="${item.id}"
                                onclick="handleItemClick(${item.id}, '${item.sale_order_id}', ${item.than_qty}, this, '${item.design?.design_code || 'N/A'}', '${item.color?.title || 'N/A'}')">
                                <strong>Sale Order #${item.sale_order_id}</strong><br>
                                Design: ${item.design.design_code || 'N/A'}<br>
                                Color: ${item.color.title || 'N/A'}<br>
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
        window.handleItemClick = function(itemId, saleOrderId, thanQty, element, designName, colorName) {
            // Check if already selected (using parentId to track the original item)
            const isSelected = selectedItems.some(item => item.parentId === itemId);
            
            if (isSelected) {
                // Remove all items with this parentId
                selectedItems = selectedItems.filter(item => item.parentId !== itemId);
                $(element).removeClass('bg-light text-muted');
                $(element).find('.float-right').remove();
            } else {
                // Prompt user for number of items to include
                const itemCount = prompt(`How many items do you want to include? (Max: ${thanQty})`, thanQty);
                
                if (itemCount === null) return; // User cancelled
                
                const count = parseInt(itemCount);
                
                if (count <= 0) return;
                
                // Add new items - one for each count
                const maxSequence = Math.max(...selectedItems.map(item => item.sequence), 0);
                for (let i = 1; i <= count; i++) {
                    selectedItems.push({
                        id: `${itemId}-${maxSequence + i}`, // Unique ID for each row
                        parentId: itemId,     // Original item ID
                        sale_order_id: saleOrderId,
                        design_name: designName,
                        color_name: colorName,
                        than_qty: thanQty,
                        sequence: maxSequence + i,            // Track position
                        lace_qty: 0,
                        weight: 0,
                        existing_designs: []
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
                                <th>Sale #</th>
                                <th>Design</th>
                                <th>Color</th>
                                <th>Designs</th>
                                <th>Lace Qty</th>
                                <th>Weight</th>
                                <th>Action</th>
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
                const inputKey = typeof item.id === 'string' && item.id.includes('-') ? item.id : `${item.parentId}-${item.sequence}`;
                
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.sale_order_id}</td>
                        <td>${item.design_name}</td>
                        <td>${item.color_name}</td>
                        <td>
                            <select class="form-control design-select" name="designs[${inputKey}][]" multiple="multiple" style="width: 100%;" data-existing='${JSON.stringify(item.existing_designs.map(d => d.id))}'>
                                <!-- Options will be loaded from controller data -->
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="lace_qty[${inputKey}]" value="${item.lace_qty}" required step="0.01" min="0">
                        </td>
                        <td>
                            <input type="number" class="form-control" name="weight[${inputKey}]" value="${item.weight}" required step="0.01" min="0">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedItem(${item.parentId})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            html += `</tbody></table></div>`;
            selectedDiv.html(html);
            
            // Initialize Select2 for all design dropdowns with preloaded data
            $('.design-select').each(function() {
                initSelect2(this);
                
                // Set existing selected values
                const existingIds = $(this).data('existing');
                if (existingIds && existingIds.length > 0) {
                    $(this).val(existingIds).trigger('change');
                }
            });
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
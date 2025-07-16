@extends('layouts.app')

@section('content')
    <style>
        /* Add these styles to your existing style section */
        .search-container {
            margin-bottom: 20px;
        }
        #searchInput {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #searchResults {
            display: none;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            margin-top: -1px;
            position: absolute;
            width: calc(100% - 30px);
            background: white;
            z-index: 1000;
        }
        #searchResults li {
            padding: 10px;
            cursor: pointer;
            list-style-type: none;
            border-bottom: 1px solid #eee;
        }
        #searchResults li:hover {
            background-color: #f5f5f5;
        }
        #selectedItems {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
        }
        #selectedItems li {
            padding: 8px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #selectedItems li:last-child {
            border-bottom: none;
        }
        .remove-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
        }
        .remove-btn:hover {
            background-color: #cc0000;
        }
        .hidden-inputs {
            display: none;
        }
    </style>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Than Supply</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('than-supplies.store') }}" method="POST" id="thanIssueForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" 
                                                   value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                        </div>
                                    </div>

                                    <!-- Search and Selected Items Section -->
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="search-container">
                                                    <h4>Add THAN Issue Items</h4>
                                                    <input type="text" id="searchInput" placeholder="Search by serial number or product...">
                                                    <ul id="searchResults"></ul>
                                                    
                                                    <h5>Selected Items</h5>
                                                    <ul id="selectedItems"></ul>
                                                    
                                                    <!-- Hidden container for form inputs -->
                                                    <div id="hiddenInputs" class="hidden-inputs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rest of your existing form fields -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Job Type</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="job_type" id="job_type_department" 
                                                           value="department" {{ old('job_type') == 'department' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="job_type_department">Department</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="job_type" id="job_type_party" 
                                                           value="party" {{ old('job_type') == 'party' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="job_type_party">Party</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="department_field" 
                                         style="{{ old('job_type') == 'department' ? '' : 'display: none;' }}">
                                        <div class="form-group">
                                            <label for="department_id">Department</label>
                                            <select class="form-control" id="department_id" name="department_id">
                                                <option value="">Select Department</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="party_field" 
                                         style="{{ old('job_type') == 'party' ? '' : 'display: none;' }}">
                                        <div class="form-group">
                                            <label for="party_id">Party</label>
                                            <select class="form-control" id="party_id" name="party_id">
                                                <option value="">Select Party</option>
                                                @foreach($parties as $party)
                                                    <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                                        {{ $party->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

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

        // Search and selection functionality
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const selectedItemsList = document.getElementById('selectedItems');
        const form = document.getElementById('thanIssueForm');
        const hiddenInputs = document.getElementById('hiddenInputs');
        
        let selectedItems = [];
        let searchTimeout = null;

        // Search items when typing
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();
            
            if (searchTerm.length < 0) {
                searchResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetchSearchResults(searchTerm);
            }, 300);
        });

        // Click outside to close search results
        document.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                searchResults.style.display = 'none';
            }
        });

        // Fetch search results from API
        const baseUrl = "{{env('APP_URL')}}"
        function fetchSearchResults(searchTerm) {
            fetch(`${baseUrl}/api/then-issue/search?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                });
        }

        // Display search results
        function displaySearchResults(items) {
            searchResults.innerHTML = '';
            
            if (items.length === 0) {
                searchResults.style.display = 'none';
                return;
            }
            
            items.forEach(item => {
                const li = document.createElement('li');
                li.textContent = `${item.serial_no} - ${item.product_group?.name || 'N/A'} (Qty: ${item.quantity})`;
                li.dataset.itemId = item.id;
                li.dataset.itemData = JSON.stringify(item);
                
                li.addEventListener('click', function() {
                    addSelectedItem(JSON.parse(this.dataset.itemData));
                    searchResults.style.display = 'none';
                    searchInput.value = '';
                });
                
                searchResults.appendChild(li);
            });
            
            searchResults.style.display = 'block';
        }

        // Add item to selected items
        function addSelectedItem(item) {
            // Check if item is already selected
            if (selectedItems.some(selected => selected.id === item.id)) {
                return;
            }
            
            selectedItems.push(item);
            updateSelectedItemsList();
            updateHiddenInputs();
        }

        // Remove item from selected items
        function removeSelectedItem(index) {
            selectedItems.splice(index, 1);
            updateSelectedItemsList();
            updateHiddenInputs();
        }

        // Update the selected items list display
        function updateSelectedItemsList() {
            selectedItemsList.innerHTML = '';
            
            if (selectedItems.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'No items selected';
                selectedItemsList.appendChild(li);
                return;
            }
            
            selectedItems.forEach((item, index) => {
                const li = document.createElement('li');
                
                const itemText = document.createElement('span');
                itemText.textContent = `${item.serial_no} - ${item.product_group?.name || 'N/A'} (Qty: ${item.quantity})`;
                
                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Remove';
                removeBtn.className = 'remove-btn';
                removeBtn.type = 'button'; // Important: prevent form submission
                removeBtn.addEventListener('click', () => removeSelectedItem(index));
                
                li.appendChild(itemText);
                li.appendChild(removeBtn);
                selectedItemsList.appendChild(li);
            });
        }

        // Update hidden form inputs
        function updateHiddenInputs() {
            hiddenInputs.innerHTML = '';
            
            selectedItems.forEach((item, index) => {
                // Create hidden inputs for each field
                createHiddenInput(`items[${index}][id]`, item.id);
                createHiddenInput(`items[${index}][than_issue_id]`, item.than_issue_id);
                createHiddenInput(`items[${index}][daily_production_item_id]`, item.daily_production_item_id);
                createHiddenInput(`items[${index}][product_group_id]`, item.product_group_id);
                createHiddenInput(`items[${index}][quantity]`, item.quantity);
                createHiddenInput(`items[${index}][serial_no]`, item.serial_no);
            });
        }

        // Helper function to create hidden inputs
        function createHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            hiddenInputs.appendChild(input);
        }

        // Initialize with empty selected items list
        updateSelectedItemsList();
    });
</script>
@endsection
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Create Batch</h3>
                        </div>
                        <div class="card-body">
                            <form id="batchForm" action="{{ route('batches.store') }}" method="POST">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference_number" class="form-label fw-bold">Reference Number</label>
                                            <input type="text" class="form-control form-control-lg" id="reference_number" name="reference_number" required placeholder="Enter reference number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="department_id" class="form-label fw-bold">Department</label>
                                            <select class="form-control form-control-lg" id="department_id" name="department_id" required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label fw-bold">Available Than Supply Items</label>
                                    <div class="search-box">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" id="item-search" class="form-control" placeholder="Search items...">
                                            <span class="input-group-text" id="selected-count">0 selected</span>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all">
                                            <label class="form-check-label" for="select-all">
                                                Select all items
                                            </label>
                                        </div>
                                    </div>
                                    <div id="items-container" class="mt-3">
                                        <p class="text-center text-muted py-5">Please select a department to view available items</p>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="button" class="btn btn-secondary me-md-2" onclick="window.history.back()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Batch</button>
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
            const departmentSelect = document.getElementById('department_id');
            const itemsContainer = document.getElementById('items-container');
            const itemSearch = document.getElementById('item-search');
            const selectAll = document.getElementById('select-all');
            const selectedCount = document.getElementById('selected-count');
            let allItems = [];
            
            // When department changes
            departmentSelect.addEventListener('change', function() {
                const departmentId = this.value;
                
                if (departmentId) {
                    // Show loading state
                    itemsContainer.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading items...</p>
                        </div>
                    `;
                    
                    // Fetch all than supply items for this department
                    fetch(`${baseUrl}/than-supply-items-by-department/${departmentId}`)
                        .then(response => response.json())
                        .then(data => {
                            allItems = data;
                            renderItems(allItems);
                            updateSelectedCount();
                        })
                        .catch(error => {
                            itemsContainer.innerHTML = `
                                <div class="alert alert-danger text-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Error loading items. Please try again.
                                </div>
                            `;
                        });
                } else {
                    itemsContainer.innerHTML = '<p class="text-center text-muted py-5">Please select a department to view available items</p>';
                    allItems = [];
                    updateSelectedCount();
                }
            });
            
            // Search functionality
            itemSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredItems = allItems.filter(item => 
                    item.serial_no.toLowerCase().includes(searchTerm) || 
                    (item.description && item.description.toLowerCase().includes(searchTerm))
                );
                renderItems(filteredItems);
            });
            
            // Select all functionality
            selectAll.addEventListener('change', function() {
                const checkboxes = itemsContainer.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    toggleItemSelection(checkbox);
                });
                updateSelectedCount();
            });
            
            // Render items in the container
            function renderItems(items) {
                if (items.length === 0) {
                    itemsContainer.innerHTML = '<p class="text-center text-muted py-5">No items found for this department</p>';
                    return;
                }
                
                itemsContainer.innerHTML = '';
                items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'item-card';
                    itemElement.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="than_supply_item_ids[]" 
                                value="${item.id}" id="item-${item.id}" onchange="toggleItemSelection(this)">
                            <label class="form-check-label w-100" for="item-${item.id}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${item.serial_no}</strong>
                                        <span class="item-details">${item.description || 'No description available'}</span>
                                    </div>
                                    <div class="text-muted">
                                        <small>Supply: ${item.than_supply_serial_no}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    `;
                    itemsContainer.appendChild(itemElement);
                });
            }
            
            // Update selected count
            function updateSelectedCount() {
                const selectedItems = itemsContainer.querySelectorAll('input[type="checkbox"]:checked');
                selectedCount.textContent = `${selectedItems.length} selected`;
            }
            
            // Global function to toggle item selection
            window.toggleItemSelection = function(checkbox) {
                const itemCard = checkbox.closest('.item-card');
                if (checkbox.checked) {
                    itemCard.classList.add('selected');
                } else {
                    itemCard.classList.remove('selected');
                    selectAll.checked = false;
                }
                updateSelectedCount();
            };
        });
    </script>

@endsection
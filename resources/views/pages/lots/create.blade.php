@extends('layouts.app')

@section('content')
<style>
    .error-message {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    .quantity-error {
        border-color: #dc3545;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Create Lot</h3>
                    </div>
                    <div class="card-body">
                        <form id="lotForm" method="POST" action="{{ route('lots.store') }}">
                            @csrf

                            {{-- Batch and Shift --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="batch_id" class="form-label">Select Batch</label>
                                    <select class="form-select" id="batch_id" name="batch_id" required>
                                        <option value="">Select Batch</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}" data-department="{{ $batch->department_id }}">
                                                {{ $batch->name }} (Department: {{ $batch->department->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="shift_id" class="form-label">Shift Machine</label>
                                    <select class="form-select" id="shift_id" name="shift_id" required>
                                        <option value="">Select Shift Machine</option>
                                        @foreach($shiftMachines as $shiftMachine)
                                            <option value="{{ $shiftMachine->id }}">{{ $shiftMachine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Times --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="run_time" class="form-label">Run Time (minutes)</label>
                                    <input type="number" class="form-control" id="run_time" name="run_time" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="temperature" class="form-label">Temperature</label>
                                    <input type="number" step="0.01" class="form-control" id="temperature" name="temperature">
                                </div>
                            </div>

                            {{-- Steam --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="steam_open" class="form-label">Steam Open</label>
                                    <input type="datetime-local" class="form-control" id="steam_open" name="steam_open">
                                </div>
                                <div class="col-md-6">
                                    <label for="steam_closed" class="form-label">Steam Closed</label>
                                    <input type="datetime-local" class="form-control" id="steam_closed" name="steam_closed">
                                </div>
                            </div>

                            {{-- Weight --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="weight" class="form-label">Weight</label>
                                    <input type="number" step="0.01" class="form-control" id="weight" name="weight">
                                </div>
                                <div class="col-md-6">
                                    <label for="total_dyeing_time" class="form-label">Total Dyeing Time (minutes)</label>
                                    <input type="number" class="form-control" id="total_dyeing_time" name="total_dyeing_time">
                                </div>
                            </div>

                            {{-- Running time --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="running_time" class="form-label">Running Time (minutes)</label>
                                    <input type="number" class="form-control" id="running_time" name="running_time">
                                </div>
                            </div>

                            {{-- Products --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4>Products</h4>
                                    <div id="products-container" class="d-none">
                                        <table class="table table-bordered" id="products-table">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Product Name</th>
                                                    <th>Available Quantity</th>
                                                    <th>Quantity to Use</th>
                                                </tr>
                                            </thead>
                                            <tbody id="products-list"></tbody>
                                        </table>
                                        <button type="button" id="add-products" class="btn btn-primary mt-2">Add Selected Products</button>
                                    </div>
                                    <div id="no-products" class="alert alert-info d-none">
                                        No products available for this department.
                                    </div>
                                </div>
                            </div>

                            {{-- Selected Products --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4>Selected Products</h4>
                                    <table class="table table-bordered" id="selected-products">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" id="submit-button" class="btn btn-primary">Create Lot</button>
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
    const batchSelect = document.getElementById('batch_id');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const runTimeInput = document.getElementById('run_time');
    const productsContainer = document.getElementById('products-container');
    const noProductsMessage = document.getElementById('no-products');
    const productsList = document.getElementById('products-list');
    const selectedProductsTable = document.querySelector('#selected-products tbody');
    const addProductsBtn = document.getElementById('add-products');
    const submitButton = document.getElementById('submit-button');
    const lotForm = document.getElementById('lotForm');

    let selectedProducts = [];
    let validationErrors = {};

    // --- Calculate run time ---
    function calculateRunTime() {
        if (startTimeInput.value && endTimeInput.value) {
            const start = new Date(startTimeInput.value);
            const end = new Date(endTimeInput.value);
            const diffMs = end - start;
            runTimeInput.value = Math.round(diffMs / 60000);
        }
    }
    startTimeInput.addEventListener('change', calculateRunTime);
    endTimeInput.addEventListener('change', calculateRunTime);

    // --- Load products ---
    batchSelect.addEventListener('change', function() {
        const departmentId = this.options[this.selectedIndex].getAttribute('data-department');
        if (!departmentId) return;

        fetch(`${baseUrl}/lot/products-by-department?department_id=${departmentId}`)
            .then(r => r.json())
            .then(products => {
                productsList.innerHTML = '';
                if (products.length > 0) {
                    productsContainer.classList.remove('d-none');
                    noProductsMessage.classList.add('d-none');
                    products.forEach(product => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><input type="checkbox" class="product-checkbox" data-id="${product.id}" data-name="${product.name}" data-max="${product.quantity}"></td>
                            <td>${product.name}</td>
                            <td>${product.quantity}</td>
                            <td>
                                <input type="number" class="form-control product-quantity" min="1" max="${product.quantity}" disabled>
                                <div class="error-message"></div>
                            </td>
                        `;
                        productsList.appendChild(row);
                    });
                } else {
                    productsContainer.classList.add('d-none');
                    noProductsMessage.classList.remove('d-none');
                }
            });
    });

    // --- Enable quantity input ---
    productsList.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-checkbox')) {
            const row = e.target.closest('tr');
            const quantityInput = row.querySelector('.product-quantity');
            const errorDiv = row.querySelector('.error-message');

            quantityInput.disabled = !e.target.checked;
            if (e.target.checked) {
                quantityInput.value = 1;
                validateQuantity(quantityInput, parseInt(e.target.dataset.max));
            } else {
                quantityInput.value = '';
                errorDiv.textContent = '';
                quantityInput.classList.remove('quantity-error');
            }
        }
    });

    // --- Quantity validation ---
    productsList.addEventListener('input', function(e) {
        if (e.target.classList.contains('product-quantity')) {
            const maxQuantity = parseInt(e.target.closest('tr').querySelector('.product-checkbox').dataset.max);
            validateQuantity(e.target, maxQuantity);
        }
    });

    function validateQuantity(input, maxQuantity) {
        const value = parseInt(input.value);
        const errorDiv = input.parentElement.querySelector('.error-message');
        const productId = input.closest('tr').querySelector('.product-checkbox').dataset.id;
        const errorKey = `product_${productId}`;

        if (isNaN(value) || value <= 0) {
            errorDiv.textContent = 'Please enter a valid quantity';
            input.classList.add('quantity-error');
            validationErrors[errorKey] = true;
            updateSubmitButtonState();
            return false;
        } else if (value > maxQuantity) {
            errorDiv.textContent = `Quantity cannot exceed ${maxQuantity}`;
            input.classList.add('quantity-error');
            validationErrors[errorKey] = true;
            updateSubmitButtonState();
            return false;
        } else {
            errorDiv.textContent = '';
            input.classList.remove('quantity-error');
            delete validationErrors[errorKey];
            updateSubmitButtonState();
            return true;
        }
    }

    function updateSubmitButtonState() {
        const hasErrors = Object.keys(validationErrors).length > 0;
        const hasProducts = selectedProducts.length > 0;
        submitButton.disabled = hasErrors || !hasProducts;
    }

    // --- Add Selected Products ---
    addProductsBtn.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        let hasErrors = false;

        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const quantityInput = row.querySelector('.product-quantity');
            const maxQuantity = parseInt(checkbox.getAttribute('data-max'));
            if (!validateQuantity(quantityInput, maxQuantity)) {
                hasErrors = true;
            }
        });

        if (hasErrors) return;

        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const productId = checkbox.dataset.id;
            const productName = checkbox.dataset.name;
            const quantityInput = row.querySelector('.product-quantity');
            const quantity = parseInt(quantityInput.value);

            // Skip if already added
            if (selectedProducts.some(p => p.id === productId)) return;

            // Add to selectedProducts array
            selectedProducts.push({ id: productId, name: productName, quantity: quantity });

            // Add row to selected products table
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${productName}<input type="hidden" name="products[${productId}][id]" value="${productId}"></td>
                <td>${quantity}<input type="hidden" name="products[${productId}][quantity]" value="${quantity}"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-product" data-id="${productId}">Remove</button></td>
            `;
            selectedProductsTable.appendChild(newRow);

            // Uncheck and reset in products list
            checkbox.checked = false;
            quantityInput.value = '';
            quantityInput.disabled = true;
        });

        updateSubmitButtonState();
    });

    // --- Remove product from selected list ---
    selectedProductsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            const productId = e.target.dataset.id;
            selectedProducts = selectedProducts.filter(p => p.id !== productId);
            e.target.closest('tr').remove();
            updateSubmitButtonState();
        }
    });

    // --- Form submission check ---
    lotForm.addEventListener('submit', function(e) {
        if (selectedProducts.length === 0) {
            e.preventDefault();
            alert('Please select at least one product.');
        } else if (Object.keys(validationErrors).length > 0) {
            e.preventDefault();
            alert('Please fix validation errors before submitting.');
        }
    });
});
</script>
@endsection


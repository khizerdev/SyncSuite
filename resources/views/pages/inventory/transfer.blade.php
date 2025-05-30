@extends('layouts.app')

@section('content')
    <div class="container card">
        <h2>Bulk Stock Transfer</h2>

        <form method="POST" action="{{ route('inventory.bulk-transfer') }}">
            @csrf

            <!-- From Department (Fixed as Main) -->
            <div class="mb-3 card">
                <label class="form-label">From Department</label>
                <input type="text" class="form-control"
                    value="{{ \App\Models\Department::where('name', 'Main')->first()->name }}" readonly>
                <input type="hidden" name="from_department"
                    value="{{ \App\Models\Department::where('name', 'Main')->first()->id }}">
            </div>

            <!-- To Department Selection -->
            <div class="mb-3 card">
                <label for="to_department" class="form-label">To Department</label>
                <select name="to_department" id="to_department" class="form-control" required>
                    <option value="">Select Target Department</option>
                    @foreach (\App\Models\Department::where('name', '!=', 'Main')->get() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Transfer Items Table -->
            <div class="mb-3 card">
                <!-- In your transfer.blade.php -->
                <table class="table" id="transferTable">
                    <thead><!-- ... --></thead>
                    <tbody>
                        @foreach (old('transfers', [['product_id' => '', 'quantity' => 1]]) as $index => $transfer)
                            <tr class="transfer-row">
                                <td>
                                    <select name="transfers[{{ $index }}][product_id]"
                                        class="form-control product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach (App\Models\Product::all() as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old("transfers.$index.product_id") == $product->id ? 'selected' : '' }}
                                                data-available="{{ $product->main_department_stock }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="available-stock">0</td>
                                <td>
                                    <input type="number" name="transfers[{{ $index }}][quantity]"
                                        class="form-control qty" min="1"
                                        value="{{ old("transfers.$index.quantity", 1) }}" required>
                                </td>
                                <td>
                                    @if ($loop->first)
                                        <button type="button" class="btn btn-secondary add-row">+</button>
                                    @else
                                        <button type="button" class="btn btn-danger remove-row">×</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" id="addRow" class="btn btn-secondary">+ Add Product</button>
            </div>

            <button type="submit" class="btn btn-primary">Transfer Stock</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add new row
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-row')) {
                    const tbody = document.querySelector('#transferTable tbody');
                    const lastRow = tbody.lastElementChild;
                    const newIndex = tbody.children.length;

                    const newRow = lastRow.cloneNode(true);

                    // Update all names/ids with new index
                    newRow.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/\[\d+\]/, `[${newIndex}]`);
                    });

                    // Reset values
                    newRow.querySelector('.product-select').value = '';
                    newRow.querySelector('.qty').value = 1;
                    newRow.querySelector('.available-stock').textContent = '0';

                    // Change button to remove
                    const btn = newRow.querySelector('.add-row');
                    btn.classList.remove('add-row', 'btn-secondary');
                    btn.classList.add('remove-row', 'btn-danger');
                    btn.textContent = '×';

                    tbody.appendChild(newRow);
                }

                // Remove row
                if (e.target.classList.contains('remove-row')) {
                    if (document.querySelectorAll('.transfer-row').length > 1) {
                        e.target.closest('tr').remove();
                    }
                }
            });

            // Update available stock when product changes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const row = e.target.closest('tr');
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    row.querySelector('.available-stock').textContent = selectedOption.dataset.available;
                    row.querySelector('.qty').max = selectedOption.dataset.available;
                }
            });
        });
    </script>
@endsection

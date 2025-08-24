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
        <div class="form-group">
            <label for="reference_number">Reference Number</label>
            <input type="text" class="form-control" id="reference_number" name="reference_number" required>
        </div>
        
        <div class="form-group">
            <label for="department_id">Department</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="than_supply_id">Than Supply</label>
            <select class="form-control" id="than_supply_id" name="than_supply_id" disabled>
                <option value="">Select Than Supply</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Than Supply Items</label>
            <div id="items-container" style="border: 1px solid #ddd; padding: 15px; max-height: 300px; overflow-y: auto;">
                <p class="text-muted">Please select a department and than supply first</p>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Batch</button>
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
    const thanSupplySelect = document.getElementById('than_supply_id');
    const itemsContainer = document.getElementById('items-container');
    
    // When department changes
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        if (departmentId) {
            thanSupplySelect.disabled = false;
            
            // Fetch than supplies for this department
            fetch(`${baseUrl}/than-supplies/${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    thanSupplySelect.innerHTML = '<option value="">Select Than Supply</option>';
                    data.forEach(supply => {
                        thanSupplySelect.innerHTML += `<option value="${supply.id}">${supply.serial_no}</option>`;
                    });
                });
                
            itemsContainer.innerHTML = '<p class="text-muted">Please select a than supply</p>';
        } else {
            thanSupplySelect.disabled = true;
            thanSupplySelect.innerHTML = '<option value="">Select Than Supply</option>';
            itemsContainer.innerHTML = '<p class="text-muted">Please select a department first</p>';
        }
    });
    
    // When than supply changes
    thanSupplySelect.addEventListener('change', function() {
        const thanSupplyId = this.value;
        
        if (thanSupplyId) {
            // Fetch than supply items
            fetch(`${baseUrl}/than-supply-items/${thanSupplyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        itemsContainer.innerHTML = '';
                        data.forEach(item => {
                            itemsContainer.innerHTML += `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="than_supply_item_ids[]" value="${item.id}" id="item-${item.id}">
                                    <label class="form-check-label" for="item-${item.id}">
                                        ${item.serial_no}
                                    </label>
                                </div>
                            `;
                        });
                    } else {
                        itemsContainer.innerHTML = '<p class="text-muted">No items found for this than supply</p>';
                    }
                });
        } else {
            itemsContainer.innerHTML = '<p class="text-muted">Please select a than supply</p>';
        }
    });
});
</script>

@endsection
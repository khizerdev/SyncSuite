@extends('layouts.app')
@section('content')
<div class="container-fluid">
   <x-content-header title="Create Product" />
   <div data-v-app="">
      <!-- Product Type Modal -->
      <div class="modal fade" id="typeModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <form class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="typeModalLabel">Create Sub Category</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label for="new_type_name">Name</label>
                     <input type="text" class="form-control" id="new_type_name" required>
                  </div>
                  
                  <div class="form-group">
                     <label for="new_type_category">Category</label>
                     <select class="form-control" id="new_type_category" required>
                        <option disabled value="">Select Category</option>
                        @foreach($particulars as $particular)
                           <option value="{{$particular->id}}">{{$particular->name}}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="typeButton">Close</button>
            <button type="button" class="btn btn-primary" onclick="createNewType()">Create</button>
         </div>
            </form>
   </div>
</div>
     
      
      <!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="categoryModalLabel">Create Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group">
                     <label for="new_category_name">Name</label>
                     <input type="text" class="form-control" id="new_category_name" required>
                  </div>
                  <div class="form-group">
                     <label for="new_type_name">Prefix</label>
                     <input type="text" class="form-control" name="prefix" id="new_prefix_name" required>
                  </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="catButton">Close</button>
            <button type="button" class="btn btn-primary" onclick="createNewCategory()">Create</button>
         </div>
      </div>
   </div>
</div>
      
      
      <div class="container-fluid">
         <div class="row">
            <div class="col-md-12">
               <div class="card card-secondary">
                  <div class="card-header">
                     <h3 class="card-title">Create Product</h3>
                  </div>
                  <div class="card-body">
                     <form method="POST" action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-2">
                           <div class="col-md-6">
                              <label for="name">Product Name</label>
                              <input type="text" class="form-control" id="name" name="name" required>
                           </div>
                        </div>
                        
                        <!-- Department and Sub-Department Selection -->
                        <div class="row mb-2">
                           <div class="col-md-6">
                              <label for="department">Department</label>
                              <select class="form-control" id="department" name="department" required onchange="loadSubDepartments(this.value)">
                                 <option disabled value="">Select Department</option>
                                 @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->title}}</option>
                                 @endforeach
                              </select>
                           </div>
                           <div class="col-md-6">
                              <label for="sub_department">Sub Department <span class="text-muted">(Optional)</span></label>
                              <select class="form-control" id="sub_department" name="sub_department" disabled>
                                 <option value="">Select Sub-Department (Optional)</option>
                              </select>
                           </div>
                        </div>
                        
                        <!-- Type (Material) and Category (Particular) Selection -->
                        <div class="row mb-2">
                           <div class="col-md-6">
                              <label for="category">Category</label>
                              <div class="input-group">
                                 <select class="form-control" id="category" name="category" required onchange="loadTypes(this.value)">
                                    <option disabled value="">Select Category</option>
                                    @foreach($particulars as $particular)
                                       <option value="{{$particular->id}}">{{$particular->name}}</option>
                                    @endforeach
                                 </select>
                                 <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#categoryModal">
                                   <i class="fas fa-plus"></i>
                                </button>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="type">Sub Category <span class="text-muted">(Optional)</span></label>
                              <div class="input-group">
                                 <select class="form-control" id="type" name="type" disabled>
                                    <option value="">Select Sub Category (Optional)</option>
                                 </select>
                                 <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#typeModal">
                                   <i class="fas fa-plus"></i>
                                </button>
                              </div>
                           </div>
                        </div>
                        
                        <div class="row mb-4">
                           <div class="col-md-2">
                              <label for="openingQuantity">Opening Quantity</label>
                              <input type="number" class="form-control" id="openingQuantity" name="opening_quantity" required>
                           </div>
                           <div class="col-md-3">
                              <label for="openingInventory">Opening Inventory Price</label>
                              <input type="number" class="form-control" id="openingInventory" name="opening_inventory" required>
                           </div>
                           <div class="col-md-2">
                              <label for="totalPrice">Total Price</label>
                              <input type="number" class="form-control" id="totalPrice" name="total_price" required readonly>
                           </div>
                           <div class="col-md-2">
                              <label for="min_qty_limit">Min Quantity Limit</label>
                              <input type="text" class="form-control" id="min_qty_limit" name="min_qty_limit" required>
                           </div>
                           <div class="col-md-3">
      <label for="unit">Unit</label>
      <select class="form-control" id="unit" name="unit" required>
         <option disabled selected value="">Select Unit</option>
         <option value="kg">Kg</option>
         <option value="piece">Piece</option>
         <option value="litre">Litre</option>
      </select>
   </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit</button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 JS Bundle (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Department and Sub-Department Functions
function loadSubDepartments(departmentId) {
    if (departmentId) {
        fetch(`${baseUrl}/sub-departments/${departmentId}`)
            .then(response => response.json())
            .then(data => {
                const subDeptSelect = document.getElementById('sub_department');
                subDeptSelect.innerHTML = '<option value="">Select Sub-Department (Optional)</option>';
                
                data.forEach(subDept => {
                    const option = document.createElement('option');
                    option.value = subDept.id;
                    option.textContent = subDept.title;
                    subDeptSelect.appendChild(option);
                });
                
                subDeptSelect.disabled = false;
            })
            .catch(error => console.error('Error:', error));
    } else {
        const subDeptSelect = document.getElementById('sub_department');
        subDeptSelect.innerHTML = '<option value="">Select Sub-Department (Optional)</option>';
        subDeptSelect.disabled = true;
    }
}

// Type (Material) and Category (Particular) Functions
function loadTypes(categoryId) {
    if (categoryId) {
        fetch(`${baseUrl}/materials-by-particular/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                const typeSelect = document.getElementById('type');
                typeSelect.innerHTML = '<option value="">Select Sub Category (Optional)</option>';
                
                data.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = `${type.name}`;
                    typeSelect.appendChild(option);
                });
                
                typeSelect.disabled = false;
            })
            .catch(error => console.error('Error:', error));
    } else {
        const typeSelect = document.getElementById('type');
        typeSelect.innerHTML = '<option value="">Select Sub Category (Optional)</option>';
        typeSelect.disabled = true;
    }
}

function createNewCategory() {
    const name = document.getElementById('new_category_name').value;
    const prefix = document.getElementById('new_prefix_name').value;
    
    fetch(`${baseUrl}/particulars/store`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name,prefix })
    })
    .then(response => response.json())
    .then(data => {
        // Add new category to select
        const categorySelect = document.getElementById('category');
        const option = document.createElement('option');
        option.value = data.particular.id;
        option.textContent = `${data.particular.name}-${data.particular.prefix}`;
        categorySelect.appendChild(option);
        
        // Select the new category
        categorySelect.value = data.id;
        
        // Close modal
        document.getElementById('catButton').click();
        
        // Clear input
        document.getElementById('new_category_name').value = '';
        
        // Trigger type loading for this new category
        loadTypes(data.id);
    })
    .catch(error => console.error('Error:', error));
}

function createNewType() {
    const name = document.getElementById('new_type_name').value;
    const categoryId = document.getElementById('new_type_category').value;
    const prefix = document.getElementById('new_prefix_name').value;
    
    fetch(`${baseUrl}/materials`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name, particular_id: categoryId, prefix })
    })
    .then(response => response.json())
    .then(data => {
        // If this type belongs to the currently selected category, add it to the types dropdown
        const currentCategory = document.getElementById('category').value;
        if (currentCategory == categoryId) {
            const typeSelect = document.getElementById('type');
            const option = document.createElement('option');
            option.value = data.material.id;
            option.textContent = `${data.material.name}-${data.material.prefix}`;
            typeSelect.appendChild(option);
            
            // Select the new type
            typeSelect.value = data.id;
        }
        
        document.getElementById('typeButton').click();
        
        // Clear inputs
        document.getElementById('new_type_name').value = '';
    })
    .catch(error => console.error('Error:', error));
}

// Calculate total price when inventory or quantity changes
document.getElementById('openingQuantity').addEventListener('input', calculateTotal);
document.getElementById('openingInventory').addEventListener('input', calculateTotal);

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('openingQuantity').value) || 0;
    const inventory = parseFloat(document.getElementById('openingInventory').value) || 0;
    document.getElementById('totalPrice').value = (quantity * inventory).toFixed(2);
}


</script>
@endsection
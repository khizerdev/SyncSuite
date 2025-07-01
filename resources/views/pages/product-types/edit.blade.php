@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">Branch</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/product-types') }}">View List</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Product Type Edit</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('product-types.update', $product_type->id) }}" method="POST" data-method="PUT">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                value="{{ $product_type->name }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="material">Material</label>
                                            <select id="material" name="material_id" class="form-control" required>
                                                <option value="">Select Material</option>
                                                @foreach (App\Models\Material::all() as $item)
                                                    <option value="{{ $item->id }}" {{$product_type->material_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="particular">Particular</label>
                                            <select id="particular" name="particular_id" class="form-control" required readonly>
                                                <option value="">Loading particulars...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-secondary">Update</button>
                                        </div>
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

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize the particular dropdown as readonly
        $('#particular').prop('readonly', true);
        
        // Function to load particulars for a material
        function loadParticulars(materialId, selectedParticularId = null) {
            var particularSelect = $('#particular');
            const baseUrl = "{{ env('APP_URL') }}"
            if (materialId) {
                $.ajax({
                    url: `${baseUrl}/getParticulars/` + materialId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        particularSelect.empty();
                        
                        if (data.length > 0) {
                            particularSelect.prop('readonly', false);
                            $.each(data, function(key, value) {
                                var isSelected = (selectedParticularId && value.particular.id == selectedParticularId) ? 'selected' : '';
                                particularSelect.append('<option value="' + value.particular.id + '" ' + isSelected + '>' + value.particular.name + '</option>');
                            });
                            
                            // If no option was selected but we have a selectedParticularId (meaning it's not in the list)
                            if (selectedParticularId && !particularSelect.val()) {
                                particularSelect.prepend('<option value="' + selectedParticularId + '" selected>Current particular (not in list)</option>');
                            }
                        } else {
                            particularSelect.append('<option value="">No particulars available for this material</option>');
                            particularSelect.prop('readonly', true);
                        }
                    },
                    error: function() {
                        particularSelect.empty();
                        particularSelect.append('<option value="">Error loading particulars</option>');
                        particularSelect.prop('readonly', true);
                        console.error('Error fetching particulars.');
                    }
                });
            } else {
                particularSelect.empty();
                particularSelect.append('<option value="">Select Material first</option>');
                particularSelect.prop('readonly', true);
            }
        }
        
        // Load particulars for the initially selected material
        var initialMaterialId = $('#material').val();
        var initialParticularId = "{{ $product_type->particular_id }}";
        if (initialMaterialId) {
            loadParticulars(initialMaterialId, initialParticularId);
        } else {
            $('#particular').html('<option value="">Select Material first</option>');
        }
        
        // Handle material change
        $('#material').on('change', function() {
            var materialId = $(this).val();
            loadParticulars(materialId);
        });
    });
</script>
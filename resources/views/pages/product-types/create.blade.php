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
                            <h3 class="card-title">Product Type Create</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('product-types.store') }}" method="POST"
                                data-method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="material">Material</label>
                                            <select id="material" name="material_id" class="form-control" required>
                                                <option value="">Select Material</option>
                                                @foreach (App\Models\Material::all() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="particular">Particular</label>
                                            <select id="particular" name="particular_id" class="form-control" required readonly>
                                                <option value="">Select Material first</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-secondary">Submit</button>
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
        
        $('#material').on('change', function() {
            var materialId = $(this).val();
            var particularSelect = $('#particular');
            const baseUrl = "{{ env('APP_URL') }}"
            if (materialId) {
                $.ajax({
                    url: `${baseUrl}/getParticulars/` + materialId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        particularSelect.empty();
                        particularSelect.append('<option value="">Select Particular</option>');
                        
                        if (data.length > 0) {
                            particularSelect.prop('readonly', false);
                            $.each(data, function(key, value) {
                                particularSelect.append('<option value="' + value.particular.id + '">' + value.particular.name + '</option>');
                            });
                        } else {
                            particularSelect.append('<option value="">No particulars available</option>');
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
        });
    });
</script>
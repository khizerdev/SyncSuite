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
                                                <option></option>
                                                @foreach (App\Models\Material::all() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="particular">Particular</label>
                                            <select id="particular" name="particular_id" class="form-control" required>
                                                <option></option>
                                                {{-- @foreach (App\Models\Particular::all() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach --}}
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
        $('#material').on('change', function() {
            var materialId = $(this).val();
            if (materialId) {
                $.ajax({
                    url: '/getParticulars/' + materialId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data)
                        $('#particular').empty();
                        $('#particular').append(
                            '<option value="">Select Particular</option>');
                        $.each(data, function(key, value) {
                            $('#particular').append('<option value="' + value
                                .particular.id + '">' + value.particular.name +
                                '</option>');
                        });
                    },
                    error: function() {
                        console.error('Error fetching particulars.');
                    }
                });
            } else {
                $('#particular').empty();
                $('#particular').append('<option value="">Select Rarticular</option>');
            }
        });
    });
</script>

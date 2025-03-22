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
                        {{-- <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/branches') }}">View List</a>
                        </li> --}}
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Raw Material</h3>

                        </div>
                        <div class="card-body">
                            <form action="{{ route('raw-materials.update', $rawMaterial->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ $rawMaterial->code }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        value="{{ $rawMaterial->product_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="complete_name" class="form-label">Complete Name</label>
                                    <input type="text" class="form-control" id="complete_name" name="complete_name"
                                        value="{{ $rawMaterial->complete_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="unit_of_measurement" class="form-label">Unit of Measurement</label>
                                    <select class="form-control" id="unit_of_measurement" name="unit_of_measurement"
                                        required>
                                        <option value="kg"
                                            {{ $rawMaterial->unit_of_measurement == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="meter"
                                            {{ $rawMaterial->unit_of_measurement == 'meter' ? 'selected' : '' }}>meter
                                        </option>
                                        <option value="piece"
                                            {{ $rawMaterial->unit_of_measurement == 'piece' ? 'selected' : '' }}>piece
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="yarn" {{ $rawMaterial->type == 'yarn' ? 'selected' : '' }}>Yarn
                                        </option>
                                        <option value="fabric" {{ $rawMaterial->type == 'fabric' ? 'selected' : '' }}>Fabric
                                        </option>
                                        <option value="machine_parts"
                                            {{ $rawMaterial->type == 'machine_parts' ? 'selected' : '' }}>Machine Parts
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="rate" class="form-label">Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="rate" name="rate"
                                        value="{{ $rawMaterial->rate }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="opening_qty" class="form-label">Opening Qty</label>
                                    <input type="number" class="form-control" id="opening_qty" name="opening_qty"
                                        value="{{ $rawMaterial->opening_qty }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('raw-materials.index') }}" class="btn btn-secondary">Cancel</a>
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
        $(document).ready(function() {


        });
    </script>
@endsection

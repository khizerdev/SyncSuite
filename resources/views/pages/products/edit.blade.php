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
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/products') }}">View List</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Product Edit</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('products.update', $product->id) }}" method="POST" data-method="PUT">
                                @csrf
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                value="{{ $product->name }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="department_id">Department</label>
                                            <select id="department_id" name="department_id" class="form-control" required>
                                                @foreach (App\Models\Department::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $product->department_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="product_type_id">Product Type</label>
                                            <select id="product_type_id" name="product_type_id" class="form-control"
                                                required>
                                                @foreach (App\Models\ProductType::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $product->product_type_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="material_id">Material</label>
                                            <select id="material_id" name="material_id" class="form-control" required>
                                                @foreach (App\Models\Material::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $product->material_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="particular_id">Particular</label>
                                            <select id="particular_id" name="particular_id" class="form-control" required>
                                                @foreach (App\Models\Particular::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $product->particular_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="qty">Opening Quantity</label>
                                            <input type="text" id="qty" name="qty" class="form-control"
                                                value="{{ $product->qty }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inventory_price">Rate</label>
                                            <input type="text" id="inventory_price" name="inventory_price"
                                                class="form-control" value="{{ $product->inventory_price }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="total_price">Total Price</label>
                                            <input type="text" id="total_price" name="total_price" class="form-control"
                                                value="{{ $product->total_price }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="min_qty_limit">Minimum Quantity Limit</label>
                                            <input type="text" id="min_qty_limit" name="min_qty_limit"
                                                class="form-control" value="{{ $product->min_qty_limit }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-secondary">Update</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection



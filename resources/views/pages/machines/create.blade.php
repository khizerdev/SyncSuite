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
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/machines') }}">View List</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Machine Create</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('machines.store') }}" method="POST" data-method="POST">
                                @csrf
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="department">Department</label>
                                            <select id="department" name="department_id" class="form-control" >
                                                @foreach (App\Models\Department::all() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="code">Machine Number</label>
                                            <input type="text" id="code" name="code" class="form-control"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="manufactured">Machine Manufactured</label>
                                            <select id="manufactured" name="manufacturer_id" class="form-control" >
                                                @foreach (App\Models\Manufacturer::all() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Machine Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                >
                                        </div>
                                    </div>

                                 

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="purchased_date">Machine Purchased Date</label>
                                            <input type="date" id="purchased_date" name="purchased_date"
                                                class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="model_date">Machine Model Date</label>
                                            <input type="date" id="model_date" name="model_date" class="form-control"
                                                >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="capacity">Machine Capacity (units)</label>
                                            <input type="number" id="capacity" name="capacity" class="form-control"
                                                >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="production_speed">Machine Production Speed</label>
                                            <input type="number" step="0.01" id="production_speed"
                                                name="production_speed" class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="price">Machine Purchase Price</label>
                                            <input type="number" step="0.01" id="price" name="price"
                                                class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="warranty_expiry">Machine Warranty Expiry</label>
                                            <input type="date" id="warranty_expiry" name="warranty" class="form-control"
                                                >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="attachments">Machine Attachments</label>
                                            <input type="file" id="attachments" name="attachments" class="form-control"
                                                >
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="remarks">Notes/Remarks</label>
                                            <textarea id="remarks" name="remarks" class="form-control" rows="4"></textarea>
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

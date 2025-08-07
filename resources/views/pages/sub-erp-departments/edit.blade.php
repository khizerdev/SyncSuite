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
                        <!--<li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/branches') }}">View List</a></li>-->
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">ERP Department Edit</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('sub-erp-departments.update', $subErpDepartment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="parent_department">Parent ERP Department*</label>
                                    <select name="department_id" id="parent_department" class="form-control @error('parent_id') is-invalid @enderror" required>
                                        <option value="">-- Select Parent Department --</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ $subErpDepartment->department_id == $department->id ? 'selected' : '' }}
                                                {{ old('parent_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="title">Sub-Department Name*</label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $subErpDepartment->title) }}" required placeholder="Enter sub-department name">
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Sub-Department</button>
                                <a href="{{ route('erp-departments.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
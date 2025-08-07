@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Sub-ERP Department</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a class="btn btn-secondary" href="{{ route('erp-departments.index') }}">Back to List</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Sub-ERP Department</h3>
                        </div>
                        <form action="{{ route('sub-erp-departments.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="parent_department">Parent ERP Department*</label>
                                    <select name="department_id" id="parent_department" class="form-control @error('parent_id') is-invalid @enderror" required>
                                        <option value="">-- Select Parent Department --</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('parent_id') == $department->id ? 'selected' : '' }}>
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
                                           value="{{ old('title') }}" required placeholder="Enter sub-department name">
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create Sub-Department</button>
                                <a href="{{ route('erp-departments.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
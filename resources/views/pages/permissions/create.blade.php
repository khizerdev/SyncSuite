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
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/permissions') }}">View List</a>
                        </li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Permissions</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('permissions.store') }}" method="POST" data-method="POST">
                                @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="role_id">Role</label>
                                        <select id="role_id" name="role_id" class="form-control"required>
                                            @foreach (App\Models\Role::get() as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @foreach (App\Models\Permission::get() as $item)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $item->id }}"
                                                    name="permissions[]" id="permission-{{ $item->id }}">
                                                <label class="form-check-label" for="permission-{{ $item->id }}">
                                                    {{ $item->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

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

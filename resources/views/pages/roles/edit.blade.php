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
                <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/roles') }}">View List</a></li>
                {{-- <li class="breadcrumb-item active">Create</li> --}}
            </ol>
        </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Role Edit</h3>
            </div>
            <div class="card-body">
                <form id="form" action="{{ route('roles.update', $role->id) }}" method="POST" data-method="PUT">
                  @csrf
                  <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $role->name }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="name">Guard Name</label>
                            <input type="text" id="guard_name" name="guard_name" class="form-control" value="{{ $role->guard_name }}">
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
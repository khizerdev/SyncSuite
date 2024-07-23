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
                <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/manufacturers') }}">View List</a></li>
                {{-- <li class="breadcrumb-item active">Create</li> --}}
            </ol>
        </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Manufacturer Edit</h3>
            </div>
            <div class="card-body">
                <form id="form" action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST" data-method="PUT">
                  @csrf
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ $manufacturer->name }}">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="name">Brand</label>
                        <input type="text" id="brand" name="brand" class="form-control" value="{{ $manufacturer->brand }}">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="name">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ $manufacturer->contact_person }}">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="name">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ $manufacturer->email }}">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="name">Contact</label>
                        <input type="text" id="contact" name="contact" class="form-control" value="{{ $manufacturer->contact }}">
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
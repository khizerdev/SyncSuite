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
                <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/materials') }}">View List</a></li>
                {{-- <li class="breadcrumb-item active">Create</li> --}}
            </ol>
        </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Material Edit</h3>
            </div>
            <div class="card-body">
                <form id="form" action="{{ route('materials.update', $material->id) }}" method="POST" data-method="PUT">
                  @csrf
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="name">Name</label>
                          <input type="text" id="name" name="name" class="form-control" value="{{$material->name}}">
                        </div>
                      </div>
                         <div class="col-sm-6">
                            <div class="form-group">
                                <label for="particular">Particular</label>
                                <select id="particular" name="particular_id" class="form-control" required>
                                @foreach (App\Models\Particular::all() as $item)
                                <option value="{{ $item->id }}" {{$material->particular_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                                @endforeach
                                </select>
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

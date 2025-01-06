@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">User</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/users') }}">View List</a></li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">User Edit</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('users.update', $user->id) }}" method="POST"
                                data-method="PUT">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Name Input -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                value="{{ $user->name }}" required>
                                        </div>
                                    </div>

                                    <!-- Email Input -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                value="{{ $user->email }}" required>
                                        </div>
                                    </div>

                                    <!-- Password Input -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Leave blank to keep current password">
                                        </div>
                                    </div>

                                    <!-- Roles Dropdown (Multiple Selection) -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="roles">Roles</label>
                                            <select id="roles" name="roles[]" class="form-control" multiple>
                                                @foreach (App\Models\Role::all() as $role)
                                                    <option value="{{ $role->id }}"
                                                        @if ($user->roles->contains($role->id)) selected @endif>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple
                                                roles.</small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
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

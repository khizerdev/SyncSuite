@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center justify-content-between">
                  <div class="col-10">
                      <h3 class="card-title">Generate Salary</h3>
                  </div>
                  
              </div>

                <div class="card-body">
                    <form action="{{ route('generate-salary.process') }}" method="POST">
                     @csrf
                        <label for="department">Select Department:</label>
                        <select name="department_id" id="department_id" class="form-control mb-2" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                           
                            <button type="submit" class="btn btn-primary">Generate </button>
                        </form>
                    </div>
                </div>

              </div>

        </div>
      </div>

    </div>
  </section>

@endsection

@section('script')

@endsection
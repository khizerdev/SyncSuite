@extends('layouts.app')

@section('css')

{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">
 
<style>
 
</style>

@endsection

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
              <div class="card-header row align-items-center">
                <div class="col-6">
                    
                    <h3 class="card-title">Employee Type</h3>
                </div>
                <div class="col-6 text-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Add New Type
                    </button>
                </div>
                </div>

                <div class="card-body">
                    <form id="employe-type-form" action="{{ route('employee-types.update', $employeeType->id) }}" method="POST" data-method="PUT">
                        @csrf
                        @method('PUT')
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employeeType->name) }}" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                                <label for="holidays" class="form-label">Holidays</label>
                                <select multiple class="holidays form-control form-select js-example-basic-single" id="holidays" name="holidays[]" required>
                                    @php
                                        $selectedHolidays = old('holidays', explode(',', $employeeType->holidays));
                                    @endphp
                                    <option value="Friday" {{ in_array('Friday', $selectedHolidays) ? 'selected' : '' }}>Friday</option>
                                    <option value="Saturday" {{ in_array('Saturday', $selectedHolidays) ? 'selected' : '' }}>Saturday</option>
                                    <option value="Sunday" {{ in_array('Sunday', $selectedHolidays) ? 'selected' : '' }}>Sunday</option>
                                    <option value="Monday" {{ in_array('Monday', $selectedHolidays) ? 'selected' : '' }}>Monday</option>
                                    <option value="Tuesday" {{ in_array('Tuesday', $selectedHolidays) ? 'selected' : '' }}>Tuesday</option>
                                    <option value="Wednesday" {{ in_array('Wednesday', $selectedHolidays) ? 'selected' : '' }}>Wednesday</option>
                                    <option value="Thursday" {{ in_array('Thursday', $selectedHolidays) ? 'selected' : '' }}>Thursday</option>
                                    <option value="No Holiday" {{ in_array('No Holiday', $selectedHolidays) ? 'selected' : '' }}>No Holiday</option>
                                </select>
                            </div>
                          </div>
                        </div>

                        <div class="mb-3">
                            <label for="holiday_ratio" class="form-label">Holiday Ratio</label>
                            <input type="number" class="form-control" id="holiday_ratio" name="holiday_ratio" step="0.01" min="0" value="{{ old('holiday_ratio', $employeeType->holiday_ratio) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Overtime</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="overtime" id="overtime_yes" value="1" {{ $employeeType->overtime == "1" ? 'checked' : '' }}>
                                <label class="form-check-label" for="overtime_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="overtime" id="overtime_no" value="0" {{ $employeeType->overtime == "0" ? 'checked' : '' }}>
                                <label class="form-check-label" for="overtime_no">No</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="overtime_ratio" class="form-label">Overtime Ratio</label>
                            <input type="number" class="form-control" id="overtime_ratio" name="overtime_ratio" step="0.01" min="0" value="{{ old('overtime_ratio', $employeeType->overtime_ratio) }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary float-right">Update</button>
                    </form>

                </div>

              </div>

        </div>
      </div>

    </div>
  </section>



@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  // In your Javascript (external .js resource or <script> tag)
$(document).ready(function() {
    $('.holidays').select2({
      theme: 'bootstrap'
});
});
</script>


@endsection
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Than Issue Create</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('than-issues.store') }}" method="POST" data-method="POST"
                                data-method="POST">
                                @csrf
                                <div class="mb-3">
            <label for="issue_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="issue_date" name="issue_date" 
                   value="{{ old('issue_date') }}" required>
        </div>
        
        <div class="mb-3">
            <label for="product_group_id" class="form-label">Product Type</label>
            <select class="form-select" id="product_group_id" name="product_group_id" required>
                <option value="">Select Product Type</option>
                @foreach($productGroups as $productGroup)
                    <option value="{{ $productGroup->id }}" {{ old('product_group_id') == $productGroup->id ? 'selected' : '' }}>
                        {{ $productGroup->code }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Job Type</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="job_type" id="job_type_department" 
                           value="department" {{ old('job_type') == 'department' ? 'checked' : '' }}>
                    <label class="form-check-label" for="job_type_department">Department</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="job_type" id="job_type_party" 
                           value="party" {{ old('job_type') == 'party' ? 'checked' : '' }}>
                    <label class="form-check-label" for="job_type_party">Party</label>
                </div>
            </div>
        </div>
        
        <div class="mb-3" id="department_field" style="{{ old('job_type') == 'department' ? '' : 'display: none;' }}">
            <label for="department_id" class="form-label">Department</label>
            <select class="form-select" id="department_id" name="department_id">
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3" id="party_field" style="{{ old('job_type') == 'party' ? '' : 'display: none;' }}">
            <label for="party_id" class="form-label">Party</label>
            <select class="form-select" id="party_id" name="party_id">
                <option value="">Select Party</option>
                @foreach($parties as $party)
                    <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                        {{ $party->name }}
                    </option>
                @endforeach
            </select>
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

@section('script')


  <script>
        $(document).ready(function() {
            // Toggle department/party fields based on job type selection
            $('input[name="job_type"]').change(function() {
                if ($(this).val() === 'department') {
                    $('#department_field').show();
                    $('#party_field').hide();
                    $('#party_id').val('');
                } else {
                    $('#department_field').hide();
                    $('#party_field').show();
                    $('#department_id').val('');
                }
            });
        });
    </script>

@endsection
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
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/employees') }}">View List</a>
                        </li>
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Employee Edit</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" id="name" name="name"
                                                value="{{ $employee->name }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="name">Father Name</label>
                                            <input type="text" id="father_name" name="father_name"
                                                value="{{ $employee->father_name }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="code">Code</label>
                                            <input type="text" id="code" name="code"
                                                value="{{ $employee->code }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="name">Passport Number</label>
                                            <input type="text" id="passport_number" name="passport_number"
                                                value="{{ $employee->passport_number }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="branch_id">Branch</label>
                                            <select id="branch_id" name="branch_id" class="form-control" required>
                                                @foreach (App\Models\Branch::all() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_id">Type</label>
                                            <select id="type_id" name="type_id" class="form-control" required>
                                                @foreach (App\Models\EmployeeType::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        @if ($employee->type_id == $item->id) selected @endif>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="department_id">Department</label>
                                            <select id="department_id" name="department_id"
                                                value="{{ $employee->department_id }}" class="form-control" required>
                                                @foreach (App\Models\Department::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        @if ($employee->department_id == $item->id) selected @endif>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="reporting_manager">Reporting Manager</label>
                                            <select id="reporting_manager" name="reporting_manager" class="form-control"
                                                required>
                                                <option value="option1" @if ($employee->reporting_manager == 'option1') selected @endif>
                                                    option1</option>
                                                <option value="option2" @if ($employee->reporting_manager == 'option2') selected @endif>
                                                    option2</option>
                                                <option value="option3" @if ($employee->reporting_manager == 'option3') selected @endif>
                                                    option3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="employement_status">Employement Status</label>
                                            <select id="employement_status" name="employement_status" class="form-control"
                                                required>
                                                <option value="option1" @if ($employee->reporting_manager == 'option1') selected @endif>
                                                    option1</option>
                                                <option value="option2" @if ($employee->reporting_manager == 'option2') selected @endif>
                                                    option2</option>
                                                <option value="option3" @if ($employee->reporting_manager == 'option3') selected @endif>
                                                    option3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="contact_number">Contact Number</label>
                                            <input type="text" id="contact_number" name="contact_number"
                                                value="{{ $employee->contact_number }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="shift_id">Shift</label>
                                            <select id="shift_id" name="shift_id" class="form-control" required>
                                                @foreach (App\Models\Shift::all() as $item)
                                                    <option value="{{ $item->id }}"
                                                        @if ($employee->shift_id == $item->id) selected @endif>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="cnic_number">CNIC Number</label>
                                            <input type="text" id="cnic_number" name="cnic_number"
                                                value="{{ $employee->cnic_number }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email"
                                                value="{{ $employee->email }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="dob">Date of Birth</label>
                                            <input type="date" id="dob" name="dob"
                                                value="{{ $employee->dob }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="hiring_date">Hiring Date</label>
                                            <input type="date" id="hiring_date" name="hiring_date"
                                                value="{{ $employee->hiring_date }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="salary">Salary</label>
                                            <input type="number" id="salary" name="salary"
                                                value="{{ $employee->salary }}" class="form-control" step="0.01"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="salary_duration">Salary Duration</label>
                                            <select id="salary_duration" name="salary_duration" class="form-control"
                                                required>
                                                <option value="half_month"
                                                    @if ($employee->salary_duration == 'half_month') selected @endif>Half Month</option>
                                                <option value="full_month"
                                                    @if ($employee->salary_duration == 'full_month') selected @endif>Full Month</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="salary_type">Salary Type</label>
                                            <select id="salary_type" name="salary_type" class="form-control" required>
                                                <option value="daily" @if ($employee->salary_type == 'daily') selected @endif>
                                                    Daily</option>
                                                <option value="monthly"
                                                    @if ($employee->salary_type == 'monthly') selected @endif>Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="profile_picture">Profile Picture</label>
                                            <input type="file" id="profile_picture" name="profile_picture"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="resume">Resume</label>
                                            <input type="file" id="resume" name="resume" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="documents">Document</label>
                                            <input type="file" id="documents" name="documents" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-secondary">Update</button>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Files</h3>
                        </div>
                        <div class="card-body">
                            <form id="form" action="{{ route('employees.update', $employee->id) }}" method="POST"
                                data-method="PUT">
                                @csrf
                                <div class="row">
                                    <div class="col-6">

                                        <form id="form" method="POST" enctype="multipart/form-data"
                                            action="{{ route('employees.update', $employee->id) }}" data-method="PUT">
                                            @csrf
                                            @method('PUT')
                                            <!-- Other form fields -->

                                            <!-- Display existing attachments -->
                                            <label for="existing_documents">Existing Documents</label>
                                            <ul>
                                                @foreach ($employee->attachments as $attachment)
                                                    <li>
                                                        <a href="{{ route('attachments.download', $attachment->id) }}">
                                                            {{ $attachment->file_name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>


                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <!-- File upload field for new documents -->
                                        <div class="form-group">
                                            <label for="documents">New Documents</label>
                                            <input type="file" id="documents" name="documents[]" class="form-control"
                                                multiple>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update</button>
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

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
                        {{-- <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/branches') }}">View List</a>
                        </li> --}}
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Customer Adjustments</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('customer-adjustments.update', $module->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Date</label>
                                            <input required value="{{ $module->date }}" name="date" type="datetime-local"
                                                class="form-control" />
                                            @if ($errors->has('date'))
                                                <div class="error text-danger">{{ $errors->first('date') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Customer</label>
                                            <select required class="form-control" name="customer_id">
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ $module->customer->id == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('type'))
                                                <div class="error text-danger">{{ $errors->first('type') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Type</label>
                                            <select required class="form-control" name="type">
                                                <option {{ $module->type == 'Credit' ? 'selected' : '' }}>Credit</option>
                                                <option {{ $module->type == 'Debit' ? 'selected' : '' }}>Debit</option>
                                            </select>
                                            @if ($errors->has('type'))
                                                <div class="error text-danger">{{ $errors->first('type') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Rate</label>
                                            <input required step=".01" value="{{ $module->rate }}" name="rate"
                                                type="number" class="form-control" />
                                            @if ($errors->has('rate'))
                                                <div class="error text-danger">{{ $errors->first('rate') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="simpleinput">Description</label>
                                            <input required step=".01" value="{{ $module->description }}"
                                                name="description" type="text" class="form-control" />
                                            @if ($errors->has('description'))
                                                <div class="error text-danger">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-center pt-3">
                                        <button type="submit" class="btn btn-info">Submit</button>
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

@section('script')
    <script>
        $(document).ready(function() {


        });
    </script>
@endsection

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
                            <h3 class="card-title">Edit Fabric Measurement</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fabric-measurements.update', $measurement->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="unit_of_measure">Unit of Measure</label>
                                    <input type="text" name="unit_of_measure" class="form-control"
                                        value="{{ $measurement->unit_of_measure }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="design_stitch">Design Stitch</label>
                                    <input type="text" name="design_stitch" class="form-control"
                                        value="{{ $measurement->design_stitch }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="front_yarn">Front Yarn</label>
                                    <input type="text" name="front_yarn" class="form-control"
                                        value="{{ $measurement->front_yarn }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="back_yarn">Back Yarn</label>
                                    <input type="text" name="back_yarn" class="form-control"
                                        value="{{ $measurement->back_yarn }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
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

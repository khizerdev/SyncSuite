@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/shifts') }}">View List</a></li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Shift Edit</h3>
                        </div>
                        <div class="card-body" id="edit-shift" data-shift="{{ json_encode($shift) }}">
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

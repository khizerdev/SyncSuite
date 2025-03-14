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
                            <h3 class="card-title">Vendor Adjustments Create</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('vendor-adjustments.store') }}">
                                @csrf
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Date</label>
                                            <input required value="{{ old('date') }}" name="date" type="datetime-local"
                                                class="form-control" />
                                            @if ($errors->has('date'))
                                                <div class="error text-danger">{{ $errors->first('date') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Vendor</label>
                                            <select required class="form-control" name="vendor_id">
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
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
                                                <option>Credit</option>
                                                <option>Debit</option>
                                            </select>
                                            @if ($errors->has('type'))
                                                <div class="error text-danger">{{ $errors->first('type') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Rate</label>
                                            <input required step=".01" value="{{ old('rate') }}" name="rate"
                                                type="number" class="form-control" />
                                            @if ($errors->has('rate'))
                                                <div class="error text-danger">{{ $errors->first('rate') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="simpleinput">Description</label>
                                            <input required step=".01" value="{{ old('description') }}"
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
    <script src="https://ahmedfabrics.com.pk/Software/public/admin/plugins/select2/select2.min.js"></script>
    <script>
        $('.js-example-basic-single').select2();
    </script>

    <script>
        $(document).ready(function() {


            // $(".title").keyup(function(){
            //     var Text = $(this).val();
            //     Text = Text.toLowerCase();
            //     Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');
            //     $(".slug").val(Text);        
            // });

        });
    </script>
@endsection

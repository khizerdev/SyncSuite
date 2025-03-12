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
                            <h3 class="card-title">Stock Edit</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('stock-adjustments.update', $module->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Product</label>
                                            <select required class="js-example-basic-single form-control" name="product_id">
                                                @foreach (\App\Models\Product::all() as $product)
                                                    <option @if ($product->id == $module->product_id) {{ 'selected' }} @endif
                                                        value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('product_id'))
                                                <div class="error text-danger">{{ $errors->first('product_id') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="simpleinput">Date</label>
                                            <input required value="{{ date('d-m-y\TH:i', strtotime($module->date)) }}"
                                                name="date" type="datetime-local" class="form-control" />
                                            @if ($errors->has('date'))
                                                <div class="error text-danger">{{ $errors->first('date') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="simpleinput">Quantity</label>
                                            <input required step=".01" value="{{ $module->qty }}" name="qty"
                                                type="number" class="form-control" />
                                            @if ($errors->has('qty'))
                                                <div class="error text-danger">{{ $errors->first('qty') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="simpleinput">Rate</label>
                                            <input required step=".01" value="{{ $module->rate }}" name="rate"
                                                type="number" class="form-control" />
                                            @if ($errors->has('rate'))
                                                <div class="error text-danger">{{ $errors->first('rate') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="simpleinput">Type</label>
                                            <select required class="form-control" name="type">
                                                <option @if ($module->type == 'Increament') {{ 'selected' }} @endif>
                                                    Increament</option>
                                                <option @if ($module->type == 'Decrement') {{ 'selected' }} @endif>
                                                    Decrement</option>
                                            </select>
                                            @if ($errors->has('type'))
                                                <div class="error text-danger">{{ $errors->first('type') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="simpleinput">Details</label>
                                            <textarea class="form-control" name="details">{{ $module->details }}</textarea>
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

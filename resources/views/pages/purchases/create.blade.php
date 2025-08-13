@extends('layouts.app')


@section('title', 'Purchases')
@section('css')

    <style>
        .normal-btn {
            background: white;
            border: none;
        }
    </style>
@endsection
@section('content')
    <script>
        var js_data = '<?php echo json_encode($products); ?>';
        var js_obj_data = JSON.parse(js_data);
    </script>



    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Create Purchase</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"> purchases</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">
                        <div class="bg-white container-fluid">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title">Create</h4>
                                </div>
                                <div class="col-6 text-right ">
                                    <a class="btn btn-primary" href="{{ route('purchases.index') }}">Back</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>



                <form method="post" action="{{ route('purchases.store') }}">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">Vendor Details</h4>
                                    </div>
                                    <div class="col-6 text-right ">

                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid">
                                <div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="simpleinput">Date</label>
                                                <input required name="date" type="date" value="{{ old('date') }}"
                                                    class="form-control" />
                                                @if ($errors->has('date'))
                                                    <div class="error text-danger">{{ $errors->first('date') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="simpleinput">Vendor</label>
                                                <select name="vendor_id" class="form-control" required>
                                                    <option value="" selected>Select Vendor</option>
                                                    @foreach ($vendors as $vendor)
                                                        <option data-address="{{ $vendor->address }}"
                                                            data-id="{{ $vendor->id }}" value="{{ $vendor->id }}">
                                                            {{ $vendor->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="simpleinput">Address</label>
                                                <input readonly type="text" class="vendor_address form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="simpleinput">Description</label>
                                                <input type="text" name="descr" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">Products</h4>
                                    </div>
                                    <div class="col-6 text-right ">

                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid">
                                <div class="pt-2 pb-3">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <select class="add-product form-control">

                                                @foreach ($products as $product)
                                                    <option data-name="{{ $product->name }}" value="{{ $product->id }}">
                                                        {{ $product->name }} - {{ $product->particular->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="add_item btn btn-primary" type="button">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <!--Item Header-->
                                <div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="simpleinput">Product</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="simpleinput">Quantity</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="simpleinput">Rate</label>
                                        </div>
                                        <div class="col-md-1 align-self-center ">

                                        </div>
                                    </div>
                                </div>
                                <!--Item Header-->


                                <div class="line-items">

                                </div>


                                <div class="row">
                                    <div class="col-md-12 pt-5 py-3 text-center">
                                        <button type="submit" class="btn btn-info">Submit</button>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    @endsection

    @section('script')

        <script>
            $(document).ready(function() {

                var index = 0;

                $(`[name='vendor_id']`).on('change', function() {

                    let customer = $(`[name='vendor_id'] option:selected`);
                    $('.vendor_id').val(customer.attr('data-id'));
                    $('.vendor_address').val(customer.attr('data-address'));
                }).change();


                //    $('.product-type').on('change', function() {

                //       $('.add-product').empty();
                //       let type = $(this).val();
                //       js_obj_data.forEach(function(index,key) {
                //             if(index.type == type){
                //               $('.add-product').append(`<option data-name="${index.title}" value="${index.id}" >${index.title}</option>`);
                //             }
                //       });
                //    }).change();    



                $('.add_item').click(function() {

                    let pid = $('.add-product').val();
                    let name = $(`.add-product option:selected`).attr('data-name');
                    if (name) {

                        let dupprocut = true;

                        $('.line-items').children().each(function() {

                            let line_item_id = $(this).find('.line_item_id').val();
                            if (line_item_id == pid) {
                                toastr.error('Can Not Add Duplicate Product');
                                dupprocut = false
                            }

                        });

                        if (dupprocut) {
                            index = index + 1;
                            $('.line-items').append(`<div class="row py-1"> 
                                <div class="col-md-6" >
                                   <input class="line_item_id" type="hidden" name="items[${index}][id]"  value="${pid}" />
                                   <input readonly name="items[${index}][name]" class="form-control" value="${name}" />
                                </div>
                                <div class="col-md-3" >
                                    <input min="1" value="1" step=".01" required name="items[${index}][qty]" type="number"  class="form-control" />
                                </div>
                                <div class="col-md-2" >
                                    <input min="1" value="1" step=".01" required name="items[${index}][rate]" type="number"  class="form-control" />
                                </div>
                                 <div class="col-md-1 align-self-center " >
                                    <button type="button" class="delete_item normal-btn d-block" ><i class="fa fa-times" ></i></button>
                                </div>
                          </div>`);
                        }
                    }

                }).click();

                $('.line-items').on("click", ".delete_item", function() {
                    $(this).parent().parent().remove();
                });

            });
        </script>
    @endsection

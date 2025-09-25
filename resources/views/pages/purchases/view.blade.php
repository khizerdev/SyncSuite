@extends('layouts.app')


@section('css')
<style>
        @media print {
            .hidden-print {
                display: none !important;
            }
            #page-topbar {
                display: none !important;
            }
            .page-content {
                padding-top:.5rem;
            }
        }
        .normal-btn{
             background: white;
            border: none;
        }

        .invoice{
            border: 1px solid;
            max-width: 1000px;
            margin: auto;
        }

        .invoice .card-body{
            padding: 0px;
        }


        /* invoice-top */

            .invoice-top{
                background-color: #3281f2;
                color: white;
                padding: 14px 0px;
            }

            .invoice-top p{
                margin: 0px;
            }

        /* invoice-top */


        /* invoice-address */

            .invoice-address{
                margin-top: 9px;
                padding: 0px;
            }

            .invoice-address .logo{
                width: 66px;
            }

            .invoice-address .title{
                font-size: 28px;
                margin: 0px 14px;
                top: 10px;
                position: relative;
                color: black;
            }

            .invoice-address h6{
                font-size: 20px;
                margin: 0px 0px;
                color: black;
            }

            .invoice-address ul{
                list-style: none;
                padding: 0px 0px;
                margin: 6px 0px 13px 0px;
                color: black;
            }

        /* invoice-address */

             /* Invoice Header */

              .invoice-header {
                padding-bottom: 17px;
            }

            .invoice-header table{
                margin: auto;
                width: 100%;
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
            }

            .invoice-header table th{
                border: 1px solid black;
                text-align: center;
                padding: 12px 0px;
                background-color: none;
                color: black;

            }

            .invoice-header table td{
                border: 1px solid #black;
                padding: 8px;
                color: black;
            }

            .invoice-header th {
                padding-top: 12px 0px;
                text-align: left;
                background-color: #3281f2;
                color: white;
            }

            hr{
                margin-top: 20px;
                margin-bottom: 20px;
                border: 0;
                border-top: 1px solid #eee;
            }
            
            .sign {
                    padding-top: 5px;
                    font-weight: bold;
                    margin: auto;
                    border-top: 1px solid black;
                    width: 122px;
                    margin-top: 10px;
            }

            .sign-text {
                padding-top: 5px;margin: auto;width: 122px;margin-top: 45px;
            }
            
            
            .order-details {
                /* display: flex; */
                flex-wrap: wrap;
                padding:5px 0px;
            }
            
            .order-details p{
                color:black;
                padding: 4px 5px;
                display:flex;
            }
            
            
            .order-details .label-value{
                border-bottom: 1px solid;   
                flex:1;
            }
            
            .order-details .label{
                padding:0px 5px;
            }
            
            
            .note{
                margin:0px;
                border: 1px solid;
                text-align: left;
                min-height: 62px;
                margin-top: 4px;
                padding-left: 11px;
                padding-top: 6px;
                color:black;
            }
</style>
@endsection
@section('content')
<div class="container-fluid">
    
   
    <!-- start page title -->
      <div class="row hidden-print">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">View Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active">purchase order</li>
                    </ol>
                </div>
            </div>
        </div>
          <div class="col-12 mb-3">
              <button class="btn btn-success btn-lg float-right" onclick="window.print();">Print</button>
          </div>
      </div>
    <!-- end page title -->

    <!-- start page title -->
  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-body"> 
                  <div class="invoice" >
                      <div class="invoice-address px-3">
                          <div style="display: flex;padding:11px 0px;" class="text-center">
                              <img src="https://ahmedfabrics.com.pk/paramount/public/uploads/logo.png" width="220" />
                              <p style="flex:1;color:black;text-align: left;margin-left: 5px;margin-bottom: 0;display: table;margin-top: auto;font-weight: 700;"   >L-4/3 Israr Ahmad Alvi St, Federal B Area Block 21 Industrial Area, Karachi, 75950, Pakistan</p>
                              <h6>PO-{{$module->date->format('ym') }}{{ str_pad(intval($module->serial), 3, '0', STR_PAD_LEFT) }}</h6>
                          </div>
                          <div class="order-details" >
                              <div class="row">
                                  <div class="col-6">
                                      <p ><span class="label">Date:</span> <span class="label-value" >{{$module->date->format('M-d-Y')}}</span></p>
                                  </div>
                                  <div class="col-6">
                                      <p ><span class="label">Vendor Name:</span><span class="label-value">{{$module->vendor->name}}</span> </p>  

                                  </div>
                                  <div class="col-6">
                                      <p ><span class="label">Vendor Address:</span><span class="label-value">{{$module->vendor->address}}</span></p>

                                  </div>
                              </div>
                              {{-- <div class="d-flex justify-content-between w-100">
                              </div> --}}
                          
                          </div>
                              <div class="invoice-header">
    <table>
        <thead>
            <tr>
                <th> # </th>
                <th> Product </th>
                <th> Unit </th>
                <th> Quantity</th>
                <th> Rate</th>
                <th> Total</th>
            </tr>        
        </thead>
        <tbody>
            @foreach($module->items as $key => $item )
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$item->product->name}}</td>
                <td>{{$item->product->unit ?? 'N/A'}}</td>
                <td>{{$item->qty}}</td>
                <td>{{number_format($item->rate, 2)}}</td>
                <td>{{number_format($item->qty * $item->rate, 2)}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>Grand Total:</strong></td>
                <td><strong>{{number_format($module->items->sum(function($item) { return $item->qty * $item->rate; }), 2)}}</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="d-flex">
        <p class="sign-text text-center text-dark"></p>          
        <p class="sign-text text-center text-dark"></p>
        <p class="sign-text text-center text-dark"></p>
        <p class="sign-text text-center text-dark"></p>          
    </div>
    <div class="d-flex">
        <p class="sign text-center text-dark">Approved by</p>
        <p class="sign text-center text-dark">Received by</p>          
    </div>
</div>
                      </div>  
                  </div>
                      
              </div>
          </div>
      </div>
     <!-- start page title -->
  </div>
@endsection

@section('script')

<script>

    $(document).ready(function () {
        
            var index = 0 ;
            
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
                
            
            
          $('.add_item').click(function (){

                let pid = $('.add-product').val();
                let name = $(`.add-product option:selected`).attr('data-name');
                console.log(name)
                let dupprocut = true;
                
                $('.line-items').children().each(function () {
                    
                    let line_item_id = $(this).find('.line_item_id').val();
                    if(line_item_id == pid ){
                      toastr.error('Can Not Add Duplicate Product');
                      dupprocut = false
                    }
                    
                });
             
                if(dupprocut){
                   index = index + 1;
                   $('.line-items').append(`<div class="row py-1"> 
                              <div class="col-md-6" >
                                 <input class="line_item_id" type="hidden" name="items[${index}][id]"  value="${pid}" />
                                 <input readonly name="items[${index}][name]" class="form-control" value="${name}" />
                              </div>
                              <div class="col-md-5" >
                                  <input min="1" value="1" step=".01" required name="items[${index}][qty]" type="number"  class="form-control" />
                              </div>
                               <div class="col-md-1 align-self-center " >
                                  <button type="button" class="delete_item normal-btn d-block" ><i class="fa fa-times" ></i></button>
                              </div>
                        </div>`);
                }
                
            }).click();
         
            $('.line-items').on("click", ".delete_item" , function() {
              $(this).parent().parent().remove();
            });
         
    });
</script>
@endsection
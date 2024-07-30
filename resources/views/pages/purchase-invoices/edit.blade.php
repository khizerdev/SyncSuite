@extends('layouts.app')
@section('title','Purchase Invoice')
@section('css')
<style>

        .normal-btn{
             background: white;
            border: none;
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
                padding-bottom: 29px;
            }

            .invoice-header table{
                margin: auto;
                width: 100%;
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
            }

            .invoice-header table thead th{
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

            .invoice-header thead th {
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

</style>
@endsection
@section('content')
<div class="container-fluid">
    
    <?php 
    
      $vendor = $invoice->items[0]->receipt->receipt->purchase->vendor;
    ?>
   
  <!-- start page title -->
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 font-size-18">Purchase Invoice Edit</h4>
          </div>
      </div>
    </div>
  <!-- end page title -->
  
      <div class="row">
        <div class="col-12">
             <form action="{{route('purchase-invoice.update',$invoice->id)}}" method="post" >
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                  <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4" >
                    		 <div class="form-group">
                    			<label for="simpleinput">SI</label>
                    			<div class="input-group">
                    			  <div class="input-group-prepend">
                    				<span class="input-group-text" >SI-{{$invoice->date->format('ym')}}</span>
                    			  </div>
                    			  <input required type="number" name="serial_no" class="form-control" value="{{ str_pad(intval($invoice->serial), 3, '0', STR_PAD_LEFT) }}" >
                    			</div>
                    			 @if($errors->has('serial_no'))
                    			  <div class="error text-danger">{{ $errors->first('serial_no') }}</div>
                    			 @endif
                    		</div>
                        </div>
                        <div class="col-md-4">
                         <label>Date</label>
                           <input type="date" required name="date" value="{{ $invoice->date->format('Y-m-d') }}"  class="form-control" />     
                        </div>
                        <div class="col-md-4">
                         <label>Due Date</label>
                           <input type="date" required name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}"  class="form-control" />     
                        </div>
                        <div class="col-md-4">
                         <label>Cartage</label>
                           <input type="number" name="cartge" value="{{$invoice->cartage ?? '0'}}"  class="cartage form-control" required/>     
                        </div>
                    </div>
                  </div>
                </div>
            </div>
            
            <div class="invoice card">
                <div class="card-body">
                       <div class="container-fluid">
                        <div class="row">
                            <div class="col-6">
                            <h4 class="card-title">Invoice</h4>
                            </div>
                            <div class="col-6 text-right "></div>
                        </div>
                      </div>
                      <div class="container-fluid">
                         
                              @csrf
                            <div class="pt-4 invoice-header">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Rceipt</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th style="width:115px;">GST</th>
                                            <th>Total</th>
                                        </tr>        
                                    </thead>
                                    <tbody class="invoice_items" >
                                        @foreach($invoice->items as $key => $invoiceItem)
                                           <?php  
                                             $receipt = $invoiceItem->receipt;
                                           ?>
                                                <tr>
                                                    <td class="text-center"><input type="hidden" name="items[{{$key}}][id]" value="{{$invoiceItem->id}}" /> {{$invoiceItem->id}}</td>
                                                    <td>{{$receipt->receipt->serial_no}}</td>
                                                    <td>{{$receipt->product->title}}</td>
                                                    <td class="text-center weight">{{$receipt->qty}}</td>
                                                    <td class="text-center rate" data-myval="{{$receipt->rate}}">{{number_format($receipt->rate,2)}}</td>
                                                    <td class="text-center"><input required min="0" name="items[{{$key}}][gst]" type="number" value="{{$invoiceItem->gst}}" class="form-control gst" /></td>
                                                    <td class="text-center total">0</td>
                                                </tr>
                                           @endforeach
                                 </tbody>
                                 <tfooter>
                                        <tr>
                                            <td style="border: 0px;" rowspan="3" colspan="5"></td>
                                            <td class="text-center">Subtotal:</td>
                                            <td class="text-center subtotal"> </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">GST:</td>
                                            <td class="text-center tgst"> </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">Grand Total:</td>
                                            <td class="text-center gtotal"> </td>
                                        </tr>
                                </tfooter>
                              </table>
                              <div class="text-center ">
                                     <input type="submit" class="mt-2 btn btn-primary" value="Submit" />    
                              </div>
                            </div>
                        
                        </div>
                   </div>
                </div>
                
                 </form>
        </div>
     </div>
 </div>
@endsection
@section('script')
      <script>        
            $(document).ready(function() {
                
               $('input').change(function(){
                          
                         
                          
                          let subtotal = 0;
                          let tgst =  0;
                          let gtotal = 0;
                          let cartage = $('.cartage').val() | 0;
                          
                         
                          
                          $('.invoice_items').children().each(function(){     
            
                            let qty = $(this).find('.weight').text();
                            // let pp = $(this).find('.rate').text();
                            let pp = $('.rate').data('myval');
                            
                            let gst = $(this).find('.gst').val() | 0;
                            
                            
                            let linetotal = qty * parseInt(pp);
                            
                            
                            subtotal += linetotal;
                            
                            gstcal = linetotal * (gst / 100);
                            linetotal = linetotal + gstcal;
                            
                            tgst += gstcal;
                            
                            $(this).find('.total').text(linetotal.toFixed(2));
                            
                            
                         });
                            
                         gtotal = subtotal + tgst + cartage;
                     
                         $('.subtotal').text(subtotal);
                         $('.tgst').text(tgst.toFixed(2));
                         $('.gtotal').text(gtotal.toFixed(2));
                           
                  });
                  
                  $("input").change();
             });
      </script>
@endsection
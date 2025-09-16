@extends('layouts.app')
@section('title','Purchase Receipt')
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
                display: flex;
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
    
    <?php $purchase = $receipt->purchase; ?>
   
      <!-- start page title -->
        <div class="row hidden-print">
          <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                  <h4 class="mb-0 font-size-18">View Purchase Receipt</h4>
                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                          <li class="breadcrumb-item active">purchase-receipt</li>
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
                                <!--<img src="{{asset('/ahmed.JPG')}}" width="220" />-->
                                <p style="flex:1;color:black;text-align: left;margin-left: 5px;margin-bottom: 0;display: table;margin-top: auto;font-weight: 700;"   >D/94, Shershah Road, SITE Karachi Tel: 325706012</p>
                                <h6>PR-{{$purchase->date->format('ym') }}{{ str_pad(intval($purchase->serial), 3, '0', STR_PAD_LEFT) }}</h6>
                            </div>
                            <div class="order-details" >
                                <div class="row">
                                    <div class="col-6">
                                        <p ><span class="label">Date:</span> <span class="label-value" >{{date('d-m-y\TH:i', strtotime($receipt->date))}}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p ><span class="label">Date:</span> <span class="label-value" >{{date('d-m-y\TH:i', strtotime($receipt->date))}}</span></p>
                                    </div>
                                    <div class="col-6">
                                        <p ><span class="label">Due Date:</span> <span class="label-value">{{$receipt->due_date->format('M-d-Y')}}</span></p>

                                    </div>
                                    <div class="col-6">
                                        <p ><span class="label">Vendor Name:</span><span class="label-value">{{$purchase->vendor->name}}</span> </p>  

                                    </div>
                                    <div class="col-6">
                                        <p ><span class="label">Vendor Address:</span><span class="label-value">{{$purchase->vendor->address}}</span></p>

                                    </div>
                                </div>
                                {{-- <div class="d-flex justify-content-between w-100">
                                </div> --}}
                            
                        </div>
                                <div class="invoice-header">
                                    <table>
                                        <thead>
                                            <tr>
                                                {{-- <th> Date </th>
                                                <th> Due Date </th>
                                                <th> Vendor Name </th>
                                                <th> Vendor Address </th> --}}
                                                <th> Product </th>
                                                <th> Quantity </th>
                                                <th> Rate </th>
                                                <th> Total</th>
                                            </tr>        
                                        </thead>
                                        <tbody>
                                            @foreach($receipt->items as $key => $item )
                                        
                                            <tr>
                                                {{-- <td class="text-center">{{date('d-m-y\TH:i', strtotime($receipt->date))}}</td>
                                                <td class="text-center">{{$receipt->due_date->format('M-d-Y')}}</td>
                                                <td class="text-center">{{$purchase->vendor->name}}</td>
                                                <td class="text-center">{{$purchase->vendor->address}}</td> --}}
                                                <td class="text-center">{{$item->product->name}}</td>
                                                <td class="text-center">{{$item->qty}}</td>
                                                <td class="text-center">{{$item->rate}}</td>
                                                <td class="text-center">{{$item->total}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex" >
                                        <p class="sign-text text-center text-dark" ></p>          
                                        <p class="sign-text text-center text-dark" ></p>
                                        <p class="sign-text text-center text-dark" ></p>
                                        <p class="sign-text text-center text-dark" ></p>          
                                    </div>
                                    <div class="d-flex" >
                                        <p class="sign text-center text-dark" >Approved by</p>
                                        <p class="sign text-center text-dark" >Recieved by</p>          
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
@section('js')
@endsection
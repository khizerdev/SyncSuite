@extends('layouts.app')
@section('title','Purchase Invoice')
@section('css')
<style>

        .normal-btn{
             background: white;
            border: none;
        }

        .invoice{
            border: 1px solid;
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
                padding-bottom: 29px;
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
                    margin-top: 45px;
            }

</style>
@endsection
@section('content')
<div class="container-fluid">


   <?php
     $vendor = $invoice->items[0]->receipt->receipt->purchase->vendor;
    
   ?>

      <div class="row px-3 mt-5">
        
        <div class="col-12">
          <div class="invoice card">
            <div class="card-body"> 
              <div class="container-fluid">

                <div class="invoice-top row">
                    <div class="col-6">
                        <h3 class="text-white">Purchase Invoice: #{{$invoice->serial_no}}<h3>   
                    </div>
                    <div class="col-6 text-right">
                        <h3 class="text-white">Date: {{$invoice->date}}<h3>
                    </div>
                </div>

                <div class="row invoice-address">
                    <div class="col-12" >
                        
                    </div>
                    <div class="col-6 text-left">
         
                        <img class="logo" style="width: 200px" src="https://ahmedfabrics.com.pk/paramount/public/uploads/logo.png" />
                        <ul>
                            <li>L-4/3 Israr Ahmad Alvi St, Federal B Area Block 21 Industrial Area, Karachi, 75950, Pakistan</li>
                        </ul>
                    </div>
                    <div class="col-6 text-right">
                        <h6>Ship To:</h6>
                        <ul>
                            <li>{{$vendor->name}} Paramount Lace</li>
                            <li>{{$vendor->address}}</li>
                            <li>{{$vendor->city}}, {{$vendor->coountry}}</li>
                            <li>NTN:{{$vendor->ntn}}</li>
                            <li>STRN:{{$vendor->strn}}</li>
                        </ul>
                    </div>
                    <div class="col-12 text-center">
                        <h6 style="font-size: 34px;">Invoice Details</h6>
                    </div>
                 </div>
                 <hr>
                      <?php  $total = 0; ?>
                            <div class="invoice-header">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Receipt</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>GST</th>
                <th>Total</th>
            </tr>        
        </thead>
        <tbody>
            <?php 
                $gsubtotal = 0;
                $ggst = 0;
                $gtotal = 0;
            ?>
            
            @foreach($invoice->items as $key => $invoiceItem)
                <?php  
                    $receiptItem = $invoiceItem->receipt;
                    $subtotal = $receiptItem->qty * $receiptItem->rate; 
                    $calgst = $subtotal * ($invoiceItem->gst / 100);
                    $total = $subtotal + $calgst; 
                    $gsubtotal += $subtotal;
                    $ggst += $calgst;
                    $gtotal += $subtotal + $calgst;
                    $party_challan = $invoiceItem->receipt->receipt->party_challan;
                ?>
                <tr>
                    <td style="width:10px;" class="text-center">{{$key + 1}}</td>
                    <td>{{$receiptItem->receipt->serial_no}} - {{$invoiceItem->receipt->receipt->purchase->serial_no}}</td>
                    <td>{{$receiptItem->product->title}}</td>
                    <td class="text-center">{{$receiptItem->qty}}</td>
                    <td class="text-center">{{$receiptItem->rate}}</td>
                    <td class="text-center">{{$invoiceItem->gst}}</td>
                    <td class="text-center">{{number_format($total,2)}}</td>
                </tr>
            @endforeach
            
            <!-- Party Challan Row -->
            <tr>
                <td colspan="7" style="text-align: left; padding: 10px;">
                    <strong>Party Challan: {{$party_challan}}</strong>
                </td>
            </tr>
            
            <!-- Description and Summary Section -->
            <tr>
                <td colspan="5" style="border: 0px; vertical-align: top; padding: 15px;">
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it.</p>
                </td>
                <td style="font-weight: bold;" class="text-center">Subtotal:</td>
                <td class="text-center">{{number_format($gsubtotal,2)}}</td>
            </tr>
            
            <tr>
                <td colspan="5" style="border: 0px;"></td>
                <td style="font-weight: bold;" class="text-center">GST:</td>
                <td style="font-weight: bold;" class="text-center">{{number_format($ggst,2)}}</td>
            </tr>
            
            <tr>
                <td colspan="5" style="border: 0px;"></td>
                <td style="font-weight: bold;" class="text-center">Cartage:</td>
                <td style="font-weight: bold;" class="text-center">{{number_format($invoice->cartage,2)}}</td>
            </tr>
            
            <tr>
                <td colspan="5" style="border: 0px;"></td>
                <td style="font-weight: bold;" class="text-center">Grand Total:</td>
                <td style="font-weight: bold;" class="text-center">{{number_format($gtotal+$invoice->cartage,2)}}</td>
            </tr>
            
            <!-- Signature Section -->
            <tr>
                <td colspan="3" style="border: none; text-align: center; padding-top: 30px;">
                    <p class="sign">Approved by</p>
                    <div style="border-top: 1px solid #000; margin-top: 20px; width: 150px; margin: 20px auto 0;"></div>
                </td>
                <td style="border: none;"></td>
                <td colspan="3" style="border: none; text-align: center; padding-top: 30px;">
                    <p class="sign">Received by</p>
                    <div style="border-top: 1px solid #000; margin-top: 20px; width: 150px; margin: 20px auto 0;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                     </div>
                </div>
            </div>
        </div>
     </div>
 </div>
@endsection
@section('script')
      <script>        
            $(document).ready(function() {
              

             });
      </script>
@endsection
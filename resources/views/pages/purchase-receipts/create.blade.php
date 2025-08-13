@extends('layouts.app')

@section('content')
<style>
    .select2-container--default .select2-selection--single {
    
    height: 41px;
}
</style>
 <div class="container-fluid">

    
    <!-- start page title -->
      <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">GRN</h4>
                
            </div>
        </div>
      </div>
    <!-- end page title -->

    <div class="mb-2 bg-white container-fluid">
      <form class="pt-3" action="{{route('purchase-receipts.create')}}" method="get">
        <div class="row" >
            <div class="col-md-11" >
              <div class="form-group">
                <select required class="js-example-basic-single form-control"  name="purchase_id" >
                    <option disabled >Select Purchase</option>
                    @foreach ($purchases as $item)
                      @if($item->receipt == null)
                         <option @if(isset($_GET['purchase_id']) && $_GET['purchase_id'] == $item->id) {{'selected'}} @endif  value="{{$item->id}}">{{$item->serial_no}} </option>
                      @endif
                    @endforeach
                    </select>
                  </div>       
                </div>
                <div class="col-md-1" >
                  <div class="form-group"><button class="btn btn-success" >Search</button></div>       
                </div>
            </div>
        </form>
      </div>

    <div class="row">
      <div class="col-12">

        @if(isset($_GET['purchase_id']))
        <?php  $purchase = \App\Models\Purchase::find($_GET['purchase_id']);  ?>
         
        <form class="myform"  method="post" action="{{route('purchase-receipts.store')}}"  >
          <input name="purchase_id" type="hidden" value="{{$purchase->id}}" class=" form-control" />
          @csrf
        
        <div class="card">
            <div class="card-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <h4 class="card-title">Purchase Order Details</h4>
                  </div>
                </div>
                <div class="row" > 
                    <div class="col-md-10" >
                        <label for="simpleinput">Product</label>    
                    </div>
                    <div class="col-md-2 text-center " >
                        <label for="simpleinput">Ordererd Qty</label>
                    </div>
                </div>
                  @if(count($purchase->items) > 0 )
                         @foreach($purchase->items as $key => $item )
                               <div class="row py-1" > 
                                  <div class="col-md-10" >
                                    <input readonly value="{{$item->product->name}} - {{$item->product->particular->name}}" type="text" class="form-control" />
                                  </div>
                                  <div class="col-md-2" >
                                    <input  readonly value="{{$item->qty}}" class="form-control" />
                                  </div>
                              </div>
                          @endforeach
                  @endif
                  <div class="row mt-3" > 
                    <div class="col-md-10" >
                        <label for="simpleinput">Party Challan</label> 
                        <input required type="text" name="party_challan" class="form-control" />
                    </div>
                </div>
              </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <h4 class="card-title">Receipt Details</h4>
                  </div>
                </div>
                <div class="row" >
                  <div class="col-md-4" >
                      <label for="simpleinput">Date</label>
                      <input required type="datetime-local" name="date" class="form-control" />
                       @if($errors->has('date'))
			                <div class="error text-danger">{{ $errors->first('date') }}</div>
			            @endif
                  </div>
                  <div class="col-md-3" >
                      <label for="simpleinput">Vendor Name</label>
                      <input readonly type="text" value="{{$purchase->vendor->name}}" class=" form-control" />     
                  </div>
                  <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="simpleinput">Description</label>
                                                <input type="text" name="descr" class="form-control" />
                                            </div>
                                        </div>
                  {{-- <div class="col-md-5" >
                      <label for="simpleinput">Vendor Address</label>
                      <input readonly type="text" value="{{$purchase->vendor->address}}"  class="form-control" />   
                  </div> --}}
              </div>
              </div>
            </div>
        </div>
        
    
        <div class="card">
          <div class="card-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <h4 class="card-title">Product Details</h4>
                  </div>
                </div>
                  <div> 
                      <div class="row" > 
                            <div class="col-md-4" >
                                <label for="simpleinput">Product</label>    
                            </div>
                            <div class="col-md-2" >
                                <label for="simpleinput">Order Qty</label>
                            </div>
                            <div class="col-md-2" >
                              <label for="simpleinput">Rcvd Qty</label>
                          </div>
                            <div class="col-md-1" >
                              <label for="simpleinput">Rate</label>
                            </div>
                            <div class="col-md-2" >
                              <label for="simpleinput">Total</label>
                            </div>
                            <div class="col-md-1 text-center " >
                              <label for="simpleinput">Action</label>
                            </div>
                      </div>
                  </div>    
                  <!--Item Header-->
                  
              <div class="line-items" >
                  @if(count($purchase->items) > 0 )
                         @foreach($purchase->items as $key => $item )
                        
                               <div class="row py-1" > 
                                  <div class="col-md-4" >
                                    <input name="items[{{$key}}][id]" value="{{$item->product->id}}" type="text" class="d-none form-control" />
                                    <input readonly value="{{$item->product->name}} - {{$item->product->particular->name}}" type="text" class="form-control" />
                                  </div>
                                  
                                  <div class="col-md-2" >
                                    <input name="items[{{$key}}][qty]" step=".01" value="{{$item->qty}}" type="number"  class="form-control" readonly/>
                                  </div>

                                  <div class="col-md-2" >
                                    <input name="items[{{$key}}][rqty]" step=".01" value="0" type="number"  class="qty form-control" required/>
                                  </div>
                                  
                                  <div class="col-md-1" >
                                    <input name="items[{{$key}}][rate]" min="1" value="{{$item->rate}}" step=".01" type="number" class="price form-control" />
                                  </div>
        
                                  <div class="col-md-2" >
                                    <input readonly type="number" value="0" step=".01" class="total form-control" />
                                  </div>
        
                                  <div class="col-md-1 text-center " >
                                    <input class="delete_item btn btn-danger" value="Delete" type="button"   />
                                  </div>
                              </div>
                          @endforeach
                 @else
                      <p  class="pt-5  text-center"> No Items Found</p>
                @endif
                </div>
                    <div class="pt-4 row" >   
                      <div class="col-md-12 text-center " >
                            <button type="submit" class="btn btn-info">Submit</button>
                      </div>
                    </div>
                </div>
              </div>
          </div>
          
         </form>
        @endif

      </div>
  </div>
  
 </div>
@endsection

@section('script')

  <script>
      $('.js-example-basic-single').select2();
  </script>
  

    @if(isset($_GET['purchase_id']))
    <script>        
      $(document).ready(function() {

             $('input').change(function(){
    
                  $('.line-items').children().each(function () {
                    
                    let qty =  $(this).find('.qty').val() | 0;
                    let pp = $(this).find('.price').val() | 0;
                    $(this).find('.total').val(pp * qty);
    
                    $(this).find('.delete-button').click(function(){
                         
                        
                    });
    
                  });
    
            });
            
             
            $('.line-items').on("click", ".delete_item" , function() {
                
                $(this).parent().parent().remove();
            });


      });
    </script>
    @endif

@endsection
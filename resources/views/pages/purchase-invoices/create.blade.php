@extends('layouts.app')



@section('content')
<div class="container-fluid">
        <!-- start page title -->
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 font-size-18">Purchase Invoice</h4>
          </div>
      </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
           <div class="card">
                  <div class="card-body">
                    <div class="container-fluid">
                      <div class="py-3 row">
                        <div class="col-6">
                          <h4 class="card-title">{{$vendor->name}} Reciepts </h4>
                        </div>
                        <div class="col-6 text-right ">
                          <button class="create_invoice btn btn-primary" >Create Invoice</button>
                        </div>
                      </div>

                      <div class="table-responsive">
                        <table class="table data-table">
                          <thead>
                              <tr>
                                  <th>Receipt</th>
                                  <th>Date</th>
                                  <th width="100px">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                          </tbody>
                      </table>
                    </div>
                    </div>
                  </div>
              </div>
          </div>
      </div>
</div>

  <form class="my_form d-none" action="{{route('purchase-invoices.add')}}" method="post" >
    @csrf
        <input type="hidden" name="vendor_id" value="{{$vendor->id}}" />
        <input type="hidden" name="receipt_id" class="receipt_id" value="" />
   </form>
@endsection
@section('script')
      <script>
        $(document).ready(function(){

            var table = $('.data-table').DataTable({
              processing: true,
              serverSide: true,
              pageLength: 100,
              ajax: "{{route('purchase-invoice.create',$vendor->id) }}",
              columns: [
                          {
                            data: 'serial_no', 
                            name: 'serial_no'
                          },
                          { 
                            data: 'date', 
                            name: 'date'
                          },
                          { 
                            data: 'action', 
                            name: 'action', 
                            orderable: false, 
                            searchable: false
                          },
                        ]
                  });


                $('.create_invoice').click(() => {
                
                          var selected = [];
                          $('tbody input:checked').each(function() {
                              selected.push($(this).val());
                          });
                          if(selected.length != 0){

                            $('.receipt_id').val(selected.toString());
                            $('.my_form').trigger('submit');
                            
                          }else{
                                toastr.warning('Please Select Receipt');
                          }
                });

          });
      </script>
@endsection
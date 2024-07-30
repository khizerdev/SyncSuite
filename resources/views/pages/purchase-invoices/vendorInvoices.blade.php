@extends('layouts.app')


@section('content')
<div class="container-fluid">


    <!-- start page title -->
        <div class="beard  row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Purchase Invoice</h4>
            </div>
        </div>
        </div>
    <!-- end page title -->

       <div class="card">
                  <div class="card-body">
                    <div class="pt-1 pb-4 container-fluid">
                        <div class=" row">
                            <div class="col-6">
                            <h4 class="card-title">{{$vendor->name}} Invoices </h4>
                            </div>
                            
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                  </div>
              </div>
</div>


@endsection

@section('script')
  

      <script>
        $(document).ready(function(){

            var table = $('.data-table').DataTable({
              processing: true,
              serverSide: true,
              pageLength: 50,
              ajax: "{{route('purchase-invoice.vendor_invoices',$vendor->id) }}",
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


          });
      </script>
@endsection
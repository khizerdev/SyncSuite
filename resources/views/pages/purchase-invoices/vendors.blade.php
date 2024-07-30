@extends('layouts.app')


@section('content')
<div class="container-fluid">

    <!-- start page title -->
        <div class="beard row">
          <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                  <h4 class="mb-0 font-size-18">Purchase Invoices</h4>
                  
              </div>
          </div>
      </div>
    <!-- end page title -->

      <div class="card">
          <div class="card-body">
            <div class="pt-1 pb-4 container-fluid">
                <div class="row">
                    <div class="col-6"><h4 class="card-title">All Vendors</h4></div>
                    <div class="col-6 text-right ">
                      <a class="btn btn-primary" href="{{route('vendors.create')}}">Add New Vendor</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table data-table">
                  <thead>
                      <tr>
                          <th style="max-width:30px;!important"  >ID</th>
                          <th style="max-width:421px;!important" >Name</th>
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
@endsection


@section('script')

<script>
    $(document).ready(function(){

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('purchase-invoice.index') }}",
            columns: [
                        {
                        data: 'id', 
                        name: 'id'
                        },
                        {
                        data: 'name', 
                        name: 'name'
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
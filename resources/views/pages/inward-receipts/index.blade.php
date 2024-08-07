@extends('layouts.app')
@section('css')
<link href="{{asset('/admin/plugins/datatables/table.css')}}" rel="stylesheet">

<style>
  table{
    width: 100%!important;
  }
</style>
@endsection
@section('content')
<div class="container-fluid">

    <!-- start page title -->
      <div class="beard row">
        <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                  <h4 class="mb-0 font-size-18">Inward Receipt</h4>
                  
              </div>
          </div>
      </div>
    <!-- end page title -->


    <!--  -->
    <div class="card">
          <div class="card-body">
            <div class=" pt-1 pb-3 container-fluid">
              <div class="row">
                <div class="col-6"><h4 class="card-title">All Inward Receipt</h4></div>
                <div class="col-6 text-right ">
                  <a class="btn btn-primary" href="{{route('inward-receipts.create')}}">Add New Receipt </a>
                </div>
              </div>
            </div>
            <div class="table-responsive">
                  <table class="data-table table mb-0">
                      <thead>
                          <tr>
                              <th class="text-left" >#</th>
                              <th class="text-left" >PO</th>
                              <th class="text-left" >Vendor</th>
                              <th class="text-left" >Date</th>
                              <th class="text-center" >Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                      
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <!--  -->
      
</div>
@endsection


@section('script')
<script>
  $(document).ready(function(){

      var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('inward-receipts.index') }}",
        columns: [
                    {
                      data: 'serial_no', 
                      name: 'serial_no'
                    },
                    {  
                      data: 'purchase', 
                      name: 'purchase'
                    },
                    {
                      data: 'vendor', 
                      name: 'vendor'
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
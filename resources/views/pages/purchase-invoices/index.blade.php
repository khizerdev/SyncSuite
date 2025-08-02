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
                    <div class="col-6"><h4 class="card-title">All Invoices</h4></div>
                    <div class="col-6 text-right ">
                      <a class="btn btn-primary" href="{{route('purchase-invoice.create')}}">Add New Invoice</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Serial No</th>
            <th>Date</th>
            <th>Due Date</th>
            <th>Cartage</th>
            <th width="150px">Action</th>
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
        ajax: "{{ route('purchase-invoice.index') }}",
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'serial_no', name: 'serial_no' },
            { data: 'date', name: 'date' },
            { data: 'due_date', name: 'due_date' },
            { data: 'cartage', name: 'cartage' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false 
            },
        ],
        order: [[2, 'desc']] // Default sort by date descending
    });
});
</script>

@endsection
@extends('layouts.app')

@section('content')

<section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center">
                    <div class="col-6">
                        
                        <h3 class="card-title">Purchase Order</h3>
                    </div>
                    <div class="col-6 text-right">
                        
                        <a class="btn btn-primary" href="{{route('purchases.create')}}">Add New Purchase Order</a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor</th>
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
  </section>
@endsection


@section('script')


   <script>
    $(document).ready(function(){
        var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{route('purchases.index') }}",
          columns: [
                      {
                        data: 'serial_no', 
                        name: 'serial_no'
                      },
                      {
                        data: 'vendor',
                        name: 'vendor_id'
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

    function deleteRecord(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
          $.ajax({
              url: '/purchases/' + id,
              type: 'GET',
              data: {
                  "_token": "{{ csrf_token() }}",
              },
              success: function(response) {
                  Swal.fire(
                      'Deleted!',
                      'Data has been deleted.',
                      'success'
                  );
                  $('.data-table').DataTable().ajax.reload();
              },
              error: function(xhr) {
                  Swal.fire(
                      'Error!',
                      'There was an error while deleting',
                      'error'
                  );
              }
          });
      }
  });
  }

  </script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <x-content-header title="Inward General" />

    <!--  -->
    <div class="card">
          <div class="card-body">
            <div class=" pt-1 pb-3 container-fluid">
              <div class="row">
                <div class="col-6"><h4 class="card-title">All Inward General</h4></div>
                <div class="col-6 text-right ">
                  <a class="btn btn-primary" href="{{route('inward-general.create')}}">Add New</a>
                </div>
              </div>
            </div>
            <div class="table-responsive">
                  <table class="data-table table mb-0">
                      <thead>
                          <tr>
                              <th class="text-left" >#</th>
                              <th class="text-left" >Reference Number</th>
                              <th class="text-left" >Party</th>
                              <th class="text-left" >Department</th>
                              <th class="text-left" >Description</th>
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
        ajax: "{{route('inward-general.index') }}",
        columns: [
                    { data: 'id', 'orderable': false, 'searchable': false },
                    {  
                      data: 'ref_number', 
                      name: 'ref_number'
                    },
                    {
                      data: 'party', 
                      name: 'party'
                    },
                    {
                      data: 'department', 
                      name: 'department'
                    },
                    {
                      data: 'description', 
                      name: 'description'
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

  const baseUrl = window.location.protocol + "//" + window.location.host; 

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
              url: `${baseUrl}/inward-general/${id}`,
              type: 'DELETE',
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
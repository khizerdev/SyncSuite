@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center">
                    <div class="col-6">
                        <h3 class="card-title">Machines</h3>
                    </div>
                    <div class="col-6 text-right">
                        <a class="btn btn-primary" href="{{ route('machines.create') }}">Add New Machine</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Code</th>
                                <th>Manufacturer</th>
                                <th>Name</th>
                                <th>Production Speed</th>
                                <th>Price</th>
                                <th>Warranty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>

              </div>

        </div>
      </div>

    </div>
  </section>

@endsection

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
      // DataTable initialization
      var dataTable = $('#table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('machines.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'department', name: 'department' },
              { data: 'code', name: 'code' },
              { data: 'manufacturer', name: 'manufacturer' },
              { data: 'name', name: 'name' },
              { data: 'production_speed', name: 'production_speed' },
              { data: 'price', name: 'price' },
              { data: 'warranty', name: 'warranty' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      
  });
</script>

@endsection
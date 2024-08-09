@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header row align-items-center">
                  <div class="col-6">
                      <h3 class="card-title">Employees</h3>
                  </div>
                  <div class="col-6 text-right">
                      <a class="btn btn-primary" href="{{ route('employees.create') }}">Add New Employee</a>
                  </div>
              </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
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
          ajax: "{{ route('employees.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'email', name: 'email' },
              { data: 'contact_number', name: 'contact_number' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

  });
</script>

@endsection
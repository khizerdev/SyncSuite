@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Products</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>name</th>
                                <th>qty</th>
                                <th>Inventory Price</th>
                                <th>Total Price</th>
                                <th>Min Quantity Limit</th>
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
          ajax: "{{ route('products.index') }}",
          columns: [
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'qty', name: 'qty' },
              { data: 'inventory_price', name: 'inventory_price' },
              { data: 'total_price', name: 'total_price' },
              { data: 'min_qty_limit', name: 'min_qty_limit' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Delete event handler
      $('#table').on('click', '.delete', function(event) {
          event.preventDefault();

          var productId = $(this).data('id');
          var row = $(this).closest('tr');

          if (confirm("Are you sure you want to delete this product?")) {
              $.ajax({
                  url: '/products/' + productId,
                  type: 'GET', // Use DELETE method for deletion
                  success: function(response) {
                      alert('Product deleted successfully');
                      dataTable.row(row).remove().draw(false); // Remove row from DataTable
                  },
                  error: function(xhr) {
                      console.error(xhr.responseText);
                      alert('Failed to delete product');
                  }
              });
          }
      });
  });
</script>

@endsection
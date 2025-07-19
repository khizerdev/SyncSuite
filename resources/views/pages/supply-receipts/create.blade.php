@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Receive Supply</h3>
                        </div>
                        <div class="card-body">
                            <form id="receiptForm" method="POST" action="{{ route('supply-receipts.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="department_id">Department</label>
            <select name="department_id" id="department_id" class="form-control" required>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Available Supplies</label>
            <div id="supplies-container">
                <!-- Supplies will be loaded here via AJAX -->
                <p class="text-muted">Please select a department first</p>
            </div>
        </div>
        
        <div class="form-group">
            <label for="received_date">Received Date</label>
            <input type="date" name="received_date" id="received_date" class="form-control" required 
                   value="{{ now()->format('Y-m-d') }}">
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
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
    $('#department_id').change(function() {
        var departmentId = $(this).val();
        const baseUrl = "{{env('APP_URL')}}"
        
        if (departmentId) {
            $.get(`${baseUrl}/supply-receipts/get-supplies`, { department_id: departmentId }, function(data) {
                var container = $('#supplies-container');
                container.empty();
                
                if (data.length > 0) {
                    var list = $('<div class="list-group"></div>');
                    
                    $.each(data, function(index, supply) {
                        var item = $(`
                            <label class="list-group-item">
                                <input type="checkbox" name="supplies[]" value="${supply.id}" class="mr-2">
                                ${supply.serial_no} - ${supply.job_type} (Issued: ${supply.issue_date})
                            </label>
                        `);
                        list.append(item);
                    });
                    
                    container.append(list);
                } else {
                    container.append('<p class="text-muted">No supplies available for receiving</p>');
                }
            });
        } else {
            $('#supplies-container').html('<p class="text-muted">Please select a department first</p>');
        }
    });
});
</script>

@endsection

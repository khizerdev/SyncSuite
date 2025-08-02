@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Purchase Invoice</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid">
                        <form id="receiptSelectionForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="receiptSelect">Select Receipt</label>
                                        <select class="form-control" id="receiptSelect" name="receipt_id" required>
                                            <option value="">-- Select Receipt --</option>
                                            @foreach($receipts as $receipt)
                                                <option value="{{ $receipt->id }}">
                                                    {{ $receipt->serial_no }} ({{ $receipt->date->format('d-m-Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-top: 30px;">
                                    <button type="button" class="btn btn-primary" id="createInvoiceBtn">
                                        Create Invoice
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Form will be loaded here -->
<div id="addFormContainer" class="d-none mt-4"></div>

@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('#createInvoiceBtn').click(function() {
            var receiptId = $('#receiptSelect').val();
            
            if(receiptId) {
                // Load the add form via AJAX
                $.ajax({
                    url: "{{ route('purchase-invoices.add') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        receipt_id: receiptId
                    },
                    beforeSend: function() {
                        $('#createInvoiceBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    },
                    success: function(response) {
                        $('#addFormContainer').html(response).removeClass('d-none');
                        $('html, body').animate({
                            scrollTop: $('#addFormContainer').offset().top
                        }, 500);
                    },
                    complete: function() {
                        $('#createInvoiceBtn').prop('disabled', false).html('Create Invoice');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.error || 'An error occurred');
                    }
                });
            } else {
                toastr.warning('Please select a receipt');
            }
        });
    });
</script>
@endsection
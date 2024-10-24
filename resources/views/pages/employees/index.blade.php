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

  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payroll Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="attdForm" action="{{ url('employees/payroll') }}" method="GET">

          <div class="form-group mb-2">
              <input type="text" class="form-control" id="employeeName" readonly>
          </div>

          <div class="form-group mb-2">
              <label for="year" class="sr-only">Select Year:</label>
              <select class="form-control" name="year" id="year" required>
                  <option value="" disabled selected>Select Year</option>
                  @foreach (range(date('Y') - 5, date('Y')) as $y)
                      <option value="{{ $y }}">{{ $y }}</option>
                  @endforeach
              </select>
          </div>

          <div class="form-group mb-2">
              <label for="month" class="sr-only">Select Month:</label>
              <select class="form-control" name="month" id="month" required>
                  <option value="" disabled selected>Select Month</option>
                  @foreach (range(1, 12) as $m)
                      <option value="{{ sprintf('%02d', $m) }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                  @endforeach
              </select>
          </div>

          <div class="form-group mb-2" id="payrollForm">
              
          </div>
          
          <button type="submit" class="btn btn-primary mb-2">Generate Slip</button>
      </form>

      </div>
    </div>
  </div>
</div>

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

      // Handle button click event
        $('#table').on('click', '.btn-show-employee', function() {
            var employeeId = $(this).data('employee-id');
            var employeeName = $(this).data('employee-name');
            $('#employeeName').val(employeeName);
            var employeeId = $(this).data('employee-id');
            $('#attdForm').attr('action', `{{ url('employees/payroll') }}/${employeeId}`);

            $('#loanField').remove();
            $('#loanCheckbox').remove();

            $.ajax({
              url: '/get-employee-loan', // Route to fetch loan details
              method: 'GET',
              data: { employee_id: employeeId },
              success: function(response) {
                  if (response.status === 'success') {
                      // If loan exists, append the loan field to the form
                      var loanFieldHtml = `
                        <div class="form-group" id="loanField">
                            <label>Loan Balance:</label>
                            <input type="text" name="loan_balance" id="loanBalance" class="form-control" readonly value="${response.loan_balance}">
                        </div>
                        <div class="form-check" id="loanCheckbox">
                            <input class="form-check-input" name="include_loan" type="checkbox" value="1" id="defaultCheck1">
                            <label class="form-check-label" for="defaultCheck1">
                              Include Loan
                            </label>
                        </div>`;
                      
                      // Append loan field before the submit button
                      $('#payrollForm').append(loanFieldHtml);
                  } 
              }
          });
        });

  });
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#year').change(function() {
        var selectedYear = $(this).val();
        var currentMonth = new Date().getMonth() + 1;

        $('#month option').each(function() {
            var monthValue = parseInt($(this).val());
            if (selectedYear == new Date().getFullYear() && monthValue > currentMonth) {
                $(this).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
            }
        });
    });

    $('#year').trigger('change');
});
</script>

@endsection
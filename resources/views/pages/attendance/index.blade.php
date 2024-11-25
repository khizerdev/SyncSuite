@extends('layouts.app')

@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Attendance View</h3>
              </div>
              <form action="{{route('attendance.view')}}" id="dateForm" class="card-body">
                <div class="col-12">
                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select id="employee_id" name="employee_id" class="form-control js-example-basic-multiple" required>
                            @foreach (App\Models\Employee::all(['id','name']) as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select id="year" name="year" class="form-control">
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 10;
                            @endphp
                            @for($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="month">Month</label>
                        <select id="month" name="month" class="form-control">
                            <option value="">Select Month</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" disabled>
                        <div class="invalid-feedback">
                            Please select a valid start date.
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" disabled>
                        <div class="invalid-feedback">
                            Please select a valid end date.
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    Submit
                </button>
              </form>
            </div>

        </div>
      </div>

    </div>
  </section>

@endsection

@section('script')

<script>

$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        width: 'resolve', // need to override the changed default
        theme: 'bootstrap4',
    });
});

$(document).ready(function() {

    // Function to get days in month
    function getDaysInMonth(year, month) {
        return new Date(year, month, 0).getDate();
    }

    function formatDate(date) {
        let d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    function updateDateInputs() {
        let selectedYear = $('#year').val();
        let selectedMonth = $('#month').val();
        
        if (selectedYear && selectedMonth) {
            let currentDate = new Date();
            let selectedDate = new Date(selectedYear, selectedMonth - 1, 1);
            
            // If selected date is in future, disable the inputs
            if (selectedDate > currentDate) {
                $('#start_date, #end_date').prop('disabled', true);
                $('#month').val('');
                alert('Cannot select future dates');
                return;
            }

            let daysInMonth = getDaysInMonth(selectedYear, selectedMonth);
            let minDate = `${selectedYear}-${selectedMonth.padStart(2, '0')}-01`;
            let maxDate = `${selectedYear}-${selectedMonth.padStart(2, '0')}-${daysInMonth}`;

            $('#start_date, #end_date').prop('disabled', false);

            // Set min and max dates
            $('#start_date, #end_date').attr({
                'min': minDate,
                'max': maxDate
            });

            $('#start_date').val('');
            $('#end_date').val('');
        } else {
            $('#start_date, #end_date').prop('disabled', true);
        }
    }

    $('#year, #month').change(updateDateInputs);

    $('#start_date').change(function() {
        let startDate = $(this).val();
        $('#end_date').attr('min', startDate);
        if ($('#end_date').val() < startDate) {
            $('#end_date').val(startDate);
        }
    });

    $('#dateForm').submit(function(e) {
        let year = $('#year').val();
        let month = $('#month').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        
        if (!year || !month || !startDate || !endDate) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        if (endDate <= startDate) {
            e.preventDefault();
            alert('End date must be greater than start date');
            return false;
        }

        return true;
    });
});
</script>


@endsection
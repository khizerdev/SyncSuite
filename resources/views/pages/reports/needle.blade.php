@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Needle Report</h2>

    <!-- Filters -->
    <form id="needle-form" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Machine</label>
            <select name="machine_id" class="form-select" required>
                <option value="">Select Machine</option>
                @foreach($machines as $machine)
                    <option value="{{ $machine->id }}">{{ $machine->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Show Report</button>
        </div>
    </form>

    <!-- Summary -->
    <div id="summary" class="row g-3 mb-4" style="display:none;">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Total</h6>
                    <h5 id="totalNeedles">0</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Average</h6>
                    <h5 id="averageNeedles">0</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Max</h6>
                    <h5 id="maxNeedles">0</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Min</h6>
                    <h5 id="minNeedles">0</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Days</h6>
                    <h5 id="productionDays">0</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div id="calendar-container">
        <div class="text-center text-muted">Select machine and date range to view report.</div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#needle-form').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('needle.report') }}",
        method: "POST",
        data: $(this).serialize(),
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        beforeSend() {
            $('#calendar-container').html('<div class="text-center py-5">Loading...</div>');
        },
        success: function(response) {
            const { summary, dailyTotals } = response;

            // Summary
            $('#summary').show();
            $('#totalNeedles').text(summary.totalNeedles);
            $('#averageNeedles').text(summary.averageNeedles);
            $('#maxNeedles').text(summary.maxNeedles);
            $('#minNeedles').text(summary.minNeedles);
            $('#productionDays').text(summary.productionDays);

            // Build calendar grid
            buildCalendar(dailyTotals);
        },
        error: function(err) {
            alert('Error loading report');
            console.log(err);
        }
    });
});

function buildCalendar(dailyTotals) {
    const dates = Object.keys(dailyTotals);
    if (dates.length === 0) {
        $('#calendar-container').html('<div class="text-center py-5 text-muted">No data found</div>');
        return;
    }

    const firstDate = new Date(dates[0]);
    const lastDate = new Date(dates[dates.length - 1]);

    let html = `
        <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
            </thead>
            <tbody>
    `;

    let currentDate = new Date(firstDate);
    currentDate.setDate(currentDate.getDate() - currentDate.getDay()); // Start from Sunday of that week

    while (currentDate <= lastDate) {
        html += '<tr>';
        for (let i = 0; i < 7; i++) {
            const dateStr = currentDate.toISOString().split('T')[0];
            const needle = dailyTotals[dateStr] !== undefined ? dailyTotals[dateStr] : '';
            html += `
                <td style="height:100px;">
                    <div class="small text-muted">${dateStr}</div>
                    <div class="fw-bold fs-6">${needle !== '' ? needle : '-'}</div>
                </td>
            `;
            currentDate.setDate(currentDate.getDate() + 1);
        }
        html += '</tr>';
    }

    html += '</tbody></table></div>';
    $('#calendar-container').html(html);
}
</script>
@endsection

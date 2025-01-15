@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">

                                <h3 class="card-title">Miss Scan</h3>
                            </div>
                            <div class="col-6 text-right">

                            </div>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="{{ route('miss-scan.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="year">Select Year:</label>
                                        <select name="year" id="year" class="form-control"
                                            onchange="this.form.submit()">
                                            @for ($i = now()->year - 5; $i <= now()->year; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="month">Select Month:</label>
                                        <select name="month" id="month" class="form-control"
                                            onchange="this.form.submit()">
                                            @foreach (range(1, 12) as $month)
                                                <option value="{{ sprintf('%02d', $month) }}"
                                                    {{ $selectedMonth == sprintf('%02d', $month) ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('miss-scan.resolve') }}">
                                @csrf
                                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                                <input type="hidden" name="year" value="{{ $selectedYear }}">

                                <table class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select-all"></th>
                                            <th>Employee</th>
                                            <th>Miss-Scan Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($missScanData as $data)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="employee_ids[]"
                                                        value="{{ $data['employee_id'] }}">
                                                </td>
                                                <td>{{ $data['employee_name'] }}</td>
                                                <td>{{ $data['miss_scan_count'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No miss-scan entries found for the
                                                    selected month and year.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($missScanData->isNotEmpty())
                                    <button type="submit" class="btn btn-success">Resolve</button>
                                @endif
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('input[name="employee_ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
        });
    </script>
@endsection

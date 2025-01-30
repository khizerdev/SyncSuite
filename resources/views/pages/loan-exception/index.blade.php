@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-6">
                                <h3 class="card-title">Loans</h3>
                            </div>
                            <div class="col-6 text-right">
                                <a class="btn btn-primary" href="{{ route('loans.create') }}">Add New Loan</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="container">
                                <form action="{{ url()->current() }}" method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="year" class="form-label">Select Year</label>
                                            <select name="year" id="year" class="form-control">
                                                @php
                                                    $currentYear = now()->year;
                                                @endphp
                                                @for ($i = $currentYear - 5; $i <= $currentYear; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ request('year') == $i ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="month" class="form-label">Select Month</label>
                                            <select name="month" id="month" class="form-control">
                                                @foreach (range(1, 12) as $m)
                                                    @php
                                                        $monthName = \Carbon\Carbon::create()->month($m)->format('F');
                                                    @endphp
                                                    <option value="{{ $m }}"
                                                        {{ request('month') == $m ? 'selected' : '' }}>
                                                        {{ $monthName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                                <form id="loan-exceptions-form" action="{{ route('loan-exception.bulk-update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all-checkbox">
                                                    </th>
                                                    <th>Employee Name</th>
                                                    <th>Department</th>
                                                    <th>Loan Amount</th>
                                                    <th>Deduction Amount (Per Month)</th>
                                                    <th>Salary Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employees as $employee)
                                                    @foreach ($employee->loanExceptions as $exception)
                                                        @php

                                                            $salary = \App\Models\Salary::where(
                                                                'employee_id',
                                                                $employee->id,
                                                            )
                                                                ->where('month', $exception->month)
                                                                ->where('year', $exception->year)
                                                                ->where('period', $exception->salary_duration)
                                                                ->first();

                                                        @endphp

                                                        @if (is_null($exception->is_approved) && !$salary)
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="selected_exceptions[]"
                                                                        class="row-checkbox"
                                                                        value="{{ $employee->id }}|{{ $exception->salary_duration }}|{{ $exception->month }}|{{ $exception->year }}">

                                                                </td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->loans->first()->amount ?? 'N/A' }}</td>
                                                                <td>{{ $employee->loans->first()->month ?? 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    @if ($exception->salary_duration == 'full_month')
                                                                        Full Month
                                                                    @elseif ($exception->salary_duration == 'first_half')
                                                                        First Half
                                                                    @elseif ($exception->salary_duration == 'second_half')
                                                                        Second Half
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr style="display:none;"></tr>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update Selected Exceptions</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const form = document.getElementById('loan-exceptions-form');

            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // prevent form submission if no checkboxes are selected
            form.addEventListener('submit', function(event) {
                const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                if (selectedCheckboxes.length === 0) {
                    event.preventDefault();
                    alert('Please select at least one exception to update.');
                    return;
                }

                // Disable unselected exception status dropdowns
                rowCheckboxes.forEach((checkbox, index) => {
                    const exceptionStatus = checkbox.closest('tr').querySelector(
                        '.exception-status');
                    if (!checkbox.checked) {
                        exceptionStatus.disabled = true;
                    }
                });
            });

            // ensure at least one checkbox is checked when it's manually unchecked
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!selectAllCheckbox.checked &&
                        document.querySelectorAll('.row-checkbox:checked').length === 0) {
                        alert('Please select at least one exception to update.');
                        this.checked = true;
                    }
                });
            });
        });
    </script>
@endsection

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
                                                    <th>Installment Per Month</th>
                                                    <th>Salary Duration</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employees as $employee)
                                                    @foreach ($employee->loanExceptions as $exception)
                                                        @if (is_null($exception->is_approved))
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="selected_exceptions[]"
                                                                        class="row-checkbox"
                                                                        value="{{ $employee->id }}|{{ $exception->salary_duration }}">
                                                                </td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->loans->first()->amount ?? 'N/A' }}</td>
                                                                <td>{{ $employee->loans->first()->amount / $employee->loans->first()->months ?? 'N/A' }}
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
                                                                <td>
                                                                    <span class="badge badge-warning">Pending</span>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select
                                                                            name="exceptions[{{ $employee->id }}][{{ $exception->salary_duration }}][approved_status]"
                                                                            class="form-control exception-status">
                                                                            <option value="approved">Approve</option>
                                                                            <option value="not_approved">Disapprove</option>
                                                                        </select>
                                                                        <input type="hidden"
                                                                            name="exceptions[{{ $employee->id }}][{{ $exception->salary_duration }}][employee_id]"
                                                                            value="{{ $employee->id }}">
                                                                        <input type="hidden"
                                                                            name="exceptions[{{ $employee->id }}][{{ $exception->salary_duration }}][month]"
                                                                            value="{{ $currentMonth }}">
                                                                        <input type="hidden"
                                                                            name="exceptions[{{ $employee->id }}][{{ $exception->salary_duration }}][year]"
                                                                            value="{{ $currentYear }}">
                                                                        <input type="hidden"
                                                                            name="exceptions[{{ $employee->id }}][{{ $exception->salary_duration }}][salary_duration]"
                                                                            value="{{ $exception->salary_duration }}">
                                                                    </div>
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

            // Select/Deselect all checkboxes
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Prevent form submission if no checkboxes are selected
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

            // Ensure at least one checkbox is checked when it's manually unchecked
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

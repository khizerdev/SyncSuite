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
                                <form action="{{ route('loan-exception.bulk-update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Employee Name</th>
                                                    <th>Department</th>
                                                    <th>Loan Amount</th>
                                                    <th>Installment Per Month</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employees as $employee)
                                                    <tr>
                                                        <td>{{ $employee->name }}</td>
                                                        <td>{{ $employee->department->name }}</td>
                                                        <td>{{ $employee->loan->amount ?? 'N/A' }}</td>
                                                        <td>{{ $employee->loan->amount/$employee->loan->months ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($employee->loanException && $employee->loanException->approved_status == 'approved')
                                                                <span class="badge badge-success">Approved</span>
                                                            @else
                                                                <span class="badge badge-danger">Not Approved</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <select
                                                                    name="exceptions[{{ $employee->id }}][approved_status]"
                                                                    class="form-control">
                                                                    <option value="approved"
                                                                        {{ $employee->loanException && $employee->loanException->approved_status == 'approved' ? 'selected' : '' }}>
                                                                        Approve</option>
                                                                    <option value="not_approved"
                                                                        {{ !$employee->loanException || $employee->loanException->approved_status != 'approved' ? 'selected' : '' }}>
                                                                        Disapprove</option>
                                                                </select>
                                                                <input type="hidden"
                                                                    name="exceptions[{{ $employee->id }}][employee_id]"
                                                                    value="{{ $employee->id }}">
                                                                <input type="hidden"
                                                                    name="exceptions[{{ $employee->id }}][month]"
                                                                    value="{{ $currentMonth }}">
                                                                <input type="hidden"
                                                                    name="exceptions[{{ $employee->id }}][year]"
                                                                    value="{{ $currentYear }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update Exceptions</button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoanException;
use App\Models\Employee;

class LoanExceptionController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Fetch employees with active loans
        $employees = Employee::with(['department', 'loans', 'loanExceptions' => function ($query) use ($currentMonth, $currentYear) {
            $query->where('month', $currentMonth)
                ->where('year', $currentYear);
        }])
        ->whereHas('loans', function ($query) {
            $query->whereColumn('paid', '<', 'amount');
        })
        ->get();

        foreach ($employees as $employee) {
            $salaryDuration = $employee->salary_duration;

            if ($salaryDuration == 'full_month') {
                // Ensure a single exception exists for `full_month`
                if (!$employee->loanExceptions->contains('salary_duration', 'full_month')) {
                    $employee->loanExceptions->push(new \App\Models\LoanException([
                        'employee_id' => $employee->id,
                        'month' => $currentMonth,
                        'year' => $currentYear,
                        'salary_duration' => 'full_month',
                        'is_approved' => null, // Default state
                    ]));
                }
            } elseif ($salaryDuration == 'half_month') {
                // Ensure two exceptions exist for `half_month` (first and second half)
                foreach (['first_half', 'second_half'] as $duration) {
                    if (!$employee->loanExceptions->contains('salary_duration', $duration)) {
                        $employee->loanExceptions->push(new \App\Models\LoanException([
                            'employee_id' => $employee->id,
                            'month' => $currentMonth,
                            'year' => $currentYear,
                            'salary_duration' => $duration,
                            'is_approved' => null, // Default state
                        ]));
                    }
                }
            }
        }

        return view('pages.loan-exception.index', compact('employees', 'currentMonth', 'currentYear'));
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->exceptions;
        foreach ($data as &$item) {
            foreach ($item as $key => $half) {
                if (!array_key_exists('approved_status', $half)) {
                    unset($item[$key]);
                }
            }
        }
        foreach ($data as $employeeId => $durations) {
            
            foreach ($durations as $salaryDuration => $exception) {
                LoanException::updateOrCreate(
                    [
                        'employee_id' => $exception['employee_id'],
                        'month' => $exception['month'],
                        'year' => $exception['year'],
                        'salary_duration' => $salaryDuration,
                    ],
                    [
                        'is_approved' => $exception['approved_status'] == 'approved' ? 1 : 0,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Loan exceptions updated successfully. Resolved records are now hidden.');
    }



}

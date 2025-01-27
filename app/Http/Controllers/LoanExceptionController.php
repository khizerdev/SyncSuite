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

        // get employees with active loans, excluding those who already have loan exceptions for the current month, year, and salary_duration
        $employees = Employee::with(['department', 'loans'])
            ->whereHas('loans', function ($query) {
                $query->whereColumn('paid', '<', 'amount');
            })
            ->whereDoesntHave('loanExceptions', function ($query) use ($currentMonth, $currentYear) {
                $query->where('month', $currentMonth)
                    ->where('year', $currentYear)
                    ->whereIn('salary_duration', ['full_month', 'first_half', 'second_half']);
            })
            ->get();

        foreach ($employees as $employee) {
            $salaryDuration = $employee->salary_duration;

            if ($salaryDuration == 'full_month') {
                if (!$employee->loanExceptions->contains('salary_duration', 'full_month')) {
                    $employee->loanExceptions->push(new \App\Models\LoanException([
                        'employee_id' => $employee->id,
                        'month' => $currentMonth,
                        'year' => $currentYear,
                        'salary_duration' => 'full_month',
                        'is_approved' => null,
                    ]));
                }
            } elseif ($salaryDuration == 'half_month') {
                foreach (['first_half', 'second_half'] as $duration) {
                    if (!$employee->loanExceptions->contains('salary_duration', $duration)) {
                        $employee->loanExceptions->push(new \App\Models\LoanException([
                            'employee_id' => $employee->id,
                            'month' => $currentMonth,
                            'year' => $currentYear,
                            'salary_duration' => $duration,
                            'is_approved' => null,
                        ]));
                    }
                }
            }
        }

        return view('pages.loan-exception.index', compact('employees', 'currentMonth', 'currentYear'));
    }

    public function bulkUpdate(Request $request)
    {
        foreach ($request->selected_exceptions as $record) {

            list($employeeId, $salaryDuration, $month, $year) = explode('|', $record);
            
            LoanException::create([
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'salary_duration' => $salaryDuration,
            ]);
        }

        return redirect()->back()->with('success', 'Success');
    }

}

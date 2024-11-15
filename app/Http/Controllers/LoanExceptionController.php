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
        // Fetch employees who have an active loan (unpaid)
        $employees = Employee::with(['department', 'loans', 'loanExceptions'])
            ->whereHas('loans', function ($query) {
                $query->whereColumn('paid', '<', 'amount'); // Only get employees whose loan is not fully paid
            })
            ->whereDoesntHave('loanExceptions', function ($query) use ($currentMonth, $currentYear) {
                $query->where('month', $currentMonth)
                      ->where('year', $currentYear);
            })
            ->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('pages.loan-exception.index', compact('employees', 'currentMonth', 'currentYear'));
    }


    public function bulkUpdate(Request $request)
    {
        
        $exceptions = $request->input('exceptions'); // Array of employee IDs and status

        if(empty($exceptions)){
            return redirect()->back()->with('error', 'No exception found');
        }

        foreach ($exceptions as $exception) {
            LoanException::updateOrCreate(
                [
                    'employee_id' => $exception['employee_id'],
                    'month' => $exception['month'],
                    'year' => $exception['year'],
                ],
                [
                    'is_approved' => $exception['approved_status'] == "approved" ? 1 : 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Loan exceptions updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Loan::latest()->get();
            return DataTables::of($data)
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name;
            })
               ->addColumn('action', function($row){
                   $editUrl = route('loans.edit', $row->id);
                   $deleteUrl = route('loans.destroy', $row->id);

                   $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                   $btn .= '<button onclick="deleteRecord(\'' . $row->id . '\', \'/loans/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2">Delete</button>';
                   return $btn;
               })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.loans.index');
    }

    public function create()
    {
        $employees = Employee::select('id', 'name', 'salary')->get();
        return view('pages.loans.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1',
        ]);

        Loan::create($validatedData);

        return redirect()->route('loans.index')->with('success', 'Loan created successfully.');
    }

    public function edit(Loan $loan)
    {
        $employees = Employee::select('id', 'name', 'salary')->get();
        return view('pages.loans.edit', compact('loan', 'employees'));
    }

    public function update(Request $request, Loan $loan)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1',
        ]);

        $loan->update($validatedData);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $branch = Loan::findOrFail($id);
            $branch->delete();
    
            return response()->json(['message' => 'Loan deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete Loan', 'error' => $e->getMessage()], 500);
        }
    }

    public function getEmployeeLoan(Request $request)
    {
        $employeeId = $request->input('employee_id');

        $activeLoan = Loan::where('employee_id', $employeeId)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

        if ($activeLoan) {
            return response()->json([
                'status' => 'success',
                'loan_balance' =>  ($activeLoan->amount - $activeLoan->balance) / $activeLoan->months,
            ]);
        } else {
            return response()->json([
                'status' => 'no_loan',
            ]);
        }
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\AdvanceSalary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdvanceSalaryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AdvanceSalary::latest()->get();
            return DataTables::of($data)
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name;
            })
               ->addColumn('action', function($row){
                   $editUrl = route('advance-salaries.edit', $row->id);
                   $deleteUrl = route('advance-salaries.destroy', $row->id);

                   $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                   $btn .= '<button onclick="deleteRecord(\'' . $row->id . '\', \'/advance-salaries/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2">Delete</button>';
                   return $btn;
               })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.advance-salaries.index');
    }

    public function create()
    {
        $employees = Employee::all();
        return view('pages.advance-salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'date' => 'required'
        ]);

        $currentMonth = now()->month;

        $existingRecord = AdvanceSalary::where('employee_id', $validatedData['employee_id'])
                                        ->where('month', $currentMonth)
                                        ->first();

        // if ($existingRecord) {
        //     return redirect()->back()->with('error', 'Already exists.');
        // }

        $validatedData['month'] = $currentMonth; 

        AdvanceSalary::create($validatedData);

        return redirect()->route('advance-salaries.index')->with('success', 'Created successfully.');
    }

    public function edit(AdvanceSalary $advanceSalary)
    {
        $employees = Employee::all();
        return view('pages.advance-salaries.edit', compact('advanceSalary', 'employees'));
    }

    public function update(Request $request, AdvanceSalary $advanceSalary)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // check if the employee_id has been changed
        if ($advanceSalary->employee_id != $validatedData['employee_id']) {
            // check if an advance salary record already exists for the new employee and the current month
            $existingRecord = AdvanceSalary::where('employee_id', $validatedData['employee_id'])
                                            ->where('month', $advanceSalary->month) 
                                            ->first();

            if ($existingRecord) {
                return redirect()->back()->with('error', 'Already exists.');
            }
        }

        $advanceSalary->update($validatedData);

        return redirect()->route('advance-salaries.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $branch = AdvanceSalary::findOrFail($id);
            $branch->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }

    public function getEmployeeSalary($id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json(['salary' => $employee->salary]);
    }
}
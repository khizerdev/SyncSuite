<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Leave::latest()->get();
            return DataTables::of($data)
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name;
            })
               ->addColumn('action', function($row){
                   $editUrl = route('leaves.edit', $row->id);
                   $deleteUrl = route('leaves.destroy', $row->id);

                   $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                   $btn .= '<button onclick="deleteRecord(\'' . $row->id . '\', \'/leaves/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2">Delete</button>';
                   return $btn;
               })
                ->rawColumns(['action'])
                ->make(true);
        }

        $employees = Employee::select('id', 'name')->get();

        return view('pages.leaves.index', compact('employees'));
    }

    public function create()
    {
        $employees = Employee::select('id', 'name')->get();
        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Leave::create($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave created successfully.');
    }

    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $employees = Employee::select('id', 'name')->get();
        return view('pages.leaves.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $leave->update($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $leave = Leave::findOrFail($id);
            $leave->delete();
    
            return response()->json(['message' => 'Leave deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete Leave', 'error' => $e->getMessage()], 500);
        }
    }
}
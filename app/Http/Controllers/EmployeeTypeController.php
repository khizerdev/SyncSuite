<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class EmployeeTypeController extends Controller
{
    
     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = EmployeeType::get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('employee-types.edit', $row->id);
                    $deleteUrl = route('employee-types.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/employee-types/\', \'DELETE\')" class="delete btn btn-danger btn-sm mr-2"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->editColumn('overtime', function ($row) {
                    return $row->overtime ? 'Yes' : 'No';
                })
                ->editColumn('adjust_hours', function ($row) {
                    return $row->adjust_hours ? 'Yes' : 'No';
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.employee-types.index');
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'holidays' => 'required|array',
            'holidays.*' => 'string',
            'overtime' => 'required|in:yes,no',
            'adjust_hours' => 'required',
            'overtime_ratio' => 'required_if:overtime,yes|nullable|numeric|min:0|max:10',
            'holiday_ratio' => 'required|numeric|min:0|max:10',
        ]);

        // Convert holidays array to JSON for storage
        $validatedData['holidays'] = implode(',', $validatedData['holidays']);

        // Convert overtime to boolean
        $validatedData['overtime'] = $validatedData['overtime'] === 'yes';
        $validatedData['adjust_hours'] = $validatedData['adjust_hours'] === 'yes';

        // Create a new EmployeeInfo instance
        EmployeeType::create($validatedData);

        return redirect()->back()->with('success', 'Success');
    } catch (Exception $e) {
        // Log the error
        // Log::error('Error saving employee information: ' . $e->getMessage());
        dd($e->getMessage());
        // You can customize this error message
        return redirect()->back()
            ->withInput()
            ->with('error', 'An error occurred while saving the employee information. Please try again.');
    }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employeeType = EmployeeType::findOrFail($id);
        return view('pages.employee-types.edit',compact('employeeType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holidays' => 'required|array',
            'holiday_ratio' => 'nullable|numeric|min:0',
            'overtime' => 'required',
            'overtime_ratio' => 'nullable|numeric|min:0',
        ]);


        $employeeType = EmployeeType::findOrFail($id);
        $employeeType->update([
            'name' => $request->name,
            'holidays' => implode(',', $request->holidays),
            'holiday_ratio' => $request->holiday_ratio,
            'overtime' => $request->overtime,
            'overtime_ratio' => $request->overtime_ratio,
            'adjust_hours' => $request->adjust_hours,
        ]);

        return redirect()->route('employee-types.index')->with('success', 'Employee type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employeeType = EmployeeType::findOrFail($id);



            if ($employeeType->employees()->exists()) {
                return response()->json([
                    'message' => 'Failed to delete',
                    'error' => 'Cannot delete Employee Type because it is associated with existing employees.'
                ], 400);
            }

            $employeeType->delete();

            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
}
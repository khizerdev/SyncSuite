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
             $data = EmployeeType::all();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('employee-types.edit', $row->id);
                    $deleteUrl = route('employee-types.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
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
            'overtime_ratio' => 'required_if:overtime,yes|nullable|numeric|min:0|max:10',
            'holiday_ratio' => 'required|numeric|min:0|max:10',
        ]);

        // Convert holidays array to JSON for storage
        $validatedData['holidays'] = implode(',', $validatedData['holidays']);

        // Convert overtime to boolean
        $validatedData['overtime'] = $validatedData['overtime'] === 'yes';

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
        $branch = Branch::findOrFail($id);
        return view('pages.branches.edit',compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);

            $validatedData = $request->validated();

            $branch->update($validatedData);


            return response()->json([
                'message' => 'Branch updated successfully',
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $branch = EmployeeType::findOrFail($id);
            $branch->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
}
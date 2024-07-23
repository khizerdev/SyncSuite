<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-department|create-department|edit-department|delete-department' => ['only' => ['index', 'store']],
             'permission:create-department' => ['only' => ['create', 'store']],
             'permission:edit-department' => ['only' => ['edit', 'update']],
             'permission:delete-department' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Department::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('departments.edit', $row->id);
                    $deleteUrl = route('departments.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.departments.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $department = Department::create($validatedData);

            return response()->json([
                'message' => 'Department created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create department',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('pages.departments.edit',compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        try {
            $department = Department::findOrFail($id);

            $validatedData = $request->validated();

            $department->update($validatedData);

            return response()->json([
                'message' => 'Department updated successfully',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update department',
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
            $department = Department::findOrFail($id);
            $department->delete();
    
            return response()->json(['message' => 'Department deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete department', 'error' => $e->getMessage()], 500);
        }
    }
    
}

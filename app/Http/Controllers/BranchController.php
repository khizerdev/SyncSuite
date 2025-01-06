<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\User;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-branch|create-branch|edit-branch|delete-branch' => ['only' => ['index', 'store']],
             'permission:create-branch' => ['only' => ['create', 'store']],
             'permission:edit-branch' => ['only' => ['edit', 'update']],
             'permission:delete-branch' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
        $user = User::find(4); // Find the user
$user->assignRole('erp'); // Assign the admin role
        $user = User::find(5); // Find the user
$user->assignRole('hr'); // Assign the admin role
$user = User::find(7); // Find the user
$user->assignRole('hr'); // Assign the admin role
$user->assignRole('erp'); // Assign the admin role
return;
         if ($request->ajax()) {
             $data = Branch::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('branches.edit', $row->id);
                    $deleteUrl = route('branches.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.branches.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $branch = Branch::create($validatedData);

            return response()->json([
                'message' => 'Branch created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create branch',
                'error' => $e->getMessage(),
            ], 500);

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
            $branch = Branch::findOrFail($id);

            if ($branch->employees()->exists()) {
                return response()->json([
                    'message' => 'Failed to delete',
                    'error' => 'Cannot delete Branch because it is associated with existing employees.'
                ], 400);
            }

            $branch->delete();
    
            return response()->json(['message' => 'Branch deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete branch', 'error' => $e->getMessage()], 500);
        }
    }
    
}
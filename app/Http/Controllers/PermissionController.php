<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-permission|create-permission|edit-permission|delete-permission' => ['only' => ['index', 'store']],
             'permission:create-permission' => ['only' => ['create', 'store']],
             'permission:edit-permission' => ['only' => ['edit', 'update']],
             'permission:delete-permission' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Permission::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('permissions.edit', $row->id);
                    $deleteUrl = route('permissions.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.permissions.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        try {

            $validatedData = $request->validated();

            
            $role = Role::findOrFail($validatedData['role_id']);
            
            $role->givePermissionTo($validatedData['permissions']);
            dd($role);

            return response()->json([
                'message' => 'Permission assigned successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create permission',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('pages.permissions.edit',compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);

            $validatedData = $request->validated();

            $permission->update($validatedData);


            return response()->json([
                'message' => 'Permission updated successfully',
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update permission',
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
            $permission = Permission::findOrFail($id);
            $permission->delete();
    
            return response()->json(['message' => 'Permission deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete permission', 'error' => $e->getMessage()], 500);
        }
    }
    
}
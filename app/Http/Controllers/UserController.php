<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //  public static function middleware(): array
    //  {
    //      return [
    //          'permission:list-user|create-user|edit-user|delete-user' => ['only' => ['index', 'store']],
    //          'permission:create-user' => ['only' => ['create', 'store']],
    //          'permission:edit-user' => ['only' => ['edit', 'update']],
    //          'permission:delete-user' => ['only' => ['destroy']],
    //      ];
    //  }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = User::whereNot('name', 'SUPER ADMIN')->latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('users.edit', $row->id);
                    $deleteUrl = route('users.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.users.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Password is required for new users
            $validatedData['password'] = Hash::make($validatedData['password']);
            
            // Extract roles from validated data
            $roleIds = $validatedData['roles'] ?? [];
            unset($validatedData['roles']);
            
            // Create user
            $user = User::create($validatedData);
            
            // Get role models from IDs and assign them
            $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
            
            return response()->json([
                'message' => 'User created successfully',
            ], 201); // Using 201 Created status code
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validatedData = $request->validated();
            
            // Handle password
            if (empty($validatedData['password'])) {
                unset($validatedData['password']);
            } else {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }
            
            // Extract roles from validated data
            $roleIds = $validatedData['roles'] ?? [];
            unset($validatedData['roles']);
            
            // Update user data
            $user->update($validatedData);
            
            // Get role models from IDs and sync them
            $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
            
            return response()->json([
                'message' => 'User and roles updated successfully',
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
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
            $user = User::findOrFail($id);
            $user->delete();
    
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        }
    }
    
}
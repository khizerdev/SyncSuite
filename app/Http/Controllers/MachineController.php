<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use App\Models\Machine;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-machine|create-machine|edit-machine|delete-machine' => ['only' => ['index', 'store']],
             'permission:create-machine' => ['only' => ['create', 'store']],
             'permission:edit-machine' => ['only' => ['edit', 'update']],
             'permission:delete-machine' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
        if ($request->ajax()) {
             $data = Machine::latest()->get();
             return DataTables::of($data)
                ->addColumn('department', function($row){
                    $department = $row->department->name;

                    return $department;
                })
                ->addColumn('manufacturer', function($row){
                    $manufacturer = $row->manufacturer->name;

                    return $manufacturer;
                })
                ->addColumn('action', function($row){
                    $editUrl = route('machines.edit', $row->id);
                    $deleteUrl = route('machines.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/machines/\', \'GET\')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
         return view('pages.machines.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.machines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMachineRequest $request)
    {
        try {
            $validatedData = $request->validated();
    
            if ($request->hasFile('attachments')) {
                $filePaths = [];
                foreach ($request->file('attachments') as $file) {
                    $filePath = Storage::disk('public')->put('machine_attachments', $file);
                    $filePaths[] = $filePath;
                }
                $validatedData['attachments'] = json_encode($filePaths);
            }
    
            $machine = Machine::create($validatedData);
    

            return response()->json([
                'message' => 'Machine created successfully',
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create machine',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $machine = Machine::findOrFail($id);
        return view('pages.machines.edit',compact('machine'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMachineRequest $request, $id)
    {
        try {
            $machine = Machine::findOrFail($id);
    
            $validatedData = $request->validated();
    
            // Handle file uploads for machine_attachments if provided
            if ($request->hasFile('machine_attachments')) {
                // Delete existing attachments if needed
                // Note: Implement your logic here based on your requirements
    
                $filePaths = [];
                foreach ($request->file('machine_attachments') as $file) {
                    $filePath = Storage::disk('public')->put('attachments', $file);
                    $filePaths[] = $filePath;
                }
                $validatedData['machine_attachments'] = json_encode($filePaths);
            }
    
            $machine->update($validatedData);
    

            return response()->json([
                'message' => 'Machine updated successfully',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update machine',
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
            $machine = Machine::findOrFail($id);
            $machine->delete();
    
            return response()->json(['message' => 'Machine deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete machine', 'error' => $e->getMessage()], 500);
        }
    }
    
}

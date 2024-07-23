<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Attachment;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-employee|create-employee|edit-employee|delete-employee' => ['only' => ['index', 'store']],
             'permission:create-employee' => ['only' => ['create', 'store']],
             'permission:edit-employee' => ['only' => ['edit', 'update']],
             'permission:delete-employee' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Employee::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('employees.edit', $row->id);
                    $deleteUrl = route('employees.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.employees.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $employee = Employee::create($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }

            
            
            // Handle Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Employee created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = Employee::with('attachments')->findOrFail($id);
        return view('pages.employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validatedData = $request->validated();

            $employee->update($validatedData);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                // Delete the old profile picture if exists
                $oldProfilePicture = $employee->attachments()->where('file_type', 'like', 'image%')->first();
                if ($oldProfilePicture) {
                    Storage::delete($oldProfilePicture->file_path);
                    $oldProfilePicture->delete();
                }

                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = $profilePicture->store('profile_pictures');

                $employee->attachments()->create([
                    'file_name' => $profilePicture->getClientOriginalName(),
                    'file_path' => $profilePicturePath,
                    'file_type' => $profilePicture->getClientMimeType(),
                    'file_size' => $profilePicture->getSize(),
                ]);
            }

            // Handle Resume
            if ($request->hasFile('resume')) {
                // Delete the old resume if exists
                $oldResume = $employee->attachments()->where('file_type', 'application/pdf')->first();
                if ($oldResume) {
                    Storage::delete($oldResume->file_path);
                    $oldResume->delete();
                }

                $resume = $request->file('resume');
                $resumePath = $resume->store('resumes');

                $employee->attachments()->create([
                    'file_name' => $resume->getClientOriginalName(),
                    'file_path' => $resumePath,
                    'file_type' => $resume->getClientMimeType(),
                    'file_size' => $resume->getSize(),
                ]);
            }


            // Handle Documents
            if ($request->hasFile('documents')) {
                // Delete the old documents if exists
                $oldDocuments = $employee->attachments()->where('file_type', '!=', 'application/pdf')->where('file_type', '!=', 'image%')->get();
                foreach ($oldDocuments as $oldDocument) {
                    Storage::delete($oldDocument->file_path);
                    $oldDocument->delete();
                }

                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('documents');

                    $employee->attachments()->create([
                        'file_name' => $document->getClientOriginalName(),
                        'file_path' => $documentPath,
                        'file_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }


            return response()->json([
                'message' => 'Employee updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee',
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
            $employee = Employee::findOrFail($id);
            $employee->delete();
    
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete employee', 'error' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);
        $pathToFile = storage_path('app/' . $attachment->file_path);

        return response()->download($pathToFile, $attachment->file_name);
    }

    
}

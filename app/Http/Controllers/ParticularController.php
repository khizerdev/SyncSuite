<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Particular\StoreParticularRequest;
use App\Http\Requests\Particular\UpdateParticularRequest;
use App\Models\Particular;

class ParticularController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-particular|create-particular|edit-particular|delete-particular' => ['only' => ['index', 'store']],
             'permission:create-particular' => ['only' => ['create', 'store']],
             'permission:edit-particular' => ['only' => ['edit', 'update']],
             'permission:delete-particular' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Particular::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('particulars.edit', $row->id);
                    $deleteUrl = route('particulars.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.particulars.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.particulars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticularRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $particular = Particular::create($validatedData);

            return response()->json([
                'message' => 'Particular created successfully',
                'particular' => $particular,
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create particular',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $particular = Particular::findOrFail($id);
        return view('pages.particulars.edit',compact('particular'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParticularRequest $request, $id)
    {
        try {
            $particular = Particular::findOrFail($id);

            $validatedData = $request->validated();

            $particular->update($validatedData);


            return response()->json([
                'message' => 'Particular updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update particular',
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
            $particular = Particular::findOrFail($id);
            $particular->delete();
    
            return response()->json(['message' => 'Particular deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete particular', 'error' => $e->getMessage()], 500);
        }
    }
    
}

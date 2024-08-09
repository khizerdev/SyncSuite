<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Manufacturer\StoreManufacturerRequest;
use App\Http\Requests\Manufacturer\UpdateManufacturerRequest;
use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-manufacturer|create-manufacturer|edit-manufacturer|delete-manufacturer' => ['only' => ['index', 'store']],
             'permission:create-manufacturer' => ['only' => ['create', 'store']],
             'permission:edit-manufacturer' => ['only' => ['edit', 'update']],
             'permission:delete-manufacturer' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Manufacturer::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('manufacturers.edit', $row->id);
                    $deleteUrl = route('manufacturers.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/manufacturers/\', \'GET\')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.manufacturers.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.manufacturers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManufacturerRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $manufacturer = Manufacturer::create($validatedData);

            return response()->json([
                'message' => 'Manufacturer created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create manufacturer',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        return view('pages.manufacturers.edit',compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManufacturerRequest $request, $id)
    {
        try {
            $manufacturer = Manufacturer::findOrFail($id);

            $validatedData = $request->validated();

            $manufacturer->update($validatedData);
            
            return response()->json([
                'message' => 'Manufacturer updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update manufacturer',
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
            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->delete();
    
            return response()->json(['message' => 'Manufacturer deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete manufacturer', 'error' => $e->getMessage()], 500);
        }
    }
    
}

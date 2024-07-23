<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Models\Material;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public static function middleware(): array
     {
         return [
             'permission:list-material|create-material|edit-material|delete-material' => ['only' => ['index', 'store']],
             'permission:create-material' => ['only' => ['create', 'store']],
             'permission:edit-material' => ['only' => ['edit', 'update']],
             'permission:delete-material' => ['only' => ['destroy']],
         ];
     }

     public function index(Request $request)
     {
         if ($request->ajax()) {
             $data = Material::latest()->get();
             return DataTables::of($data)
                ->addColumn('particular', function($row){
                    $particular = $row->particular->name;
                    return $particular;
                })
                ->addColumn('action', function($row){
                    $editUrl = route('materials.edit', $row->id);
                    $deleteUrl = route('materials.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="'.$deleteUrl.'" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
         }
         return view('pages.materials.index');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaterialRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $material = Material::create($validatedData);

            return response()->json([
                'message' => 'Material created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create material',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        return view('pages.materials.edit',compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, $id)
    {
        try {
            $material = Material::findOrFail($id);

            $validatedData = $request->validated();

            $material->update($validatedData);


            return response()->json([
                'message' => 'Material updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update material',
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
            $material = Material::findOrFail($id);
            $material->delete();
    
            return response()->json(['message' => 'Material deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete material', 'error' => $e->getMessage()], 500);
        }
    }
    
}
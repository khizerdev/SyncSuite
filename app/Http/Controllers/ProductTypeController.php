<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductType\UpdateProductTypeRequest;
use App\Http\Requests\ProductType\StoreProductTypeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use DataTables;
use App\Models\ProductType;
use App\Models\Material;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public static function middleware(): array
    {
        return [
            'permission:list-product-type|create-product-type|edit-product-type|delete-product-type' => ['only' => ['index', 'store']],
            'permission:create-product-type' => ['only' => ['create', 'store']],
            'permission:edit-product-type' => ['only' => ['edit', 'update']],
            'permission:delete-product-type' => ['only' => ['destroy']],
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductType::latest()->get();
            return DataTables::of($data)
                ->addColumn('material', function ($row) {
                    $material = $row->material->name;
                    return $material;
                })
                ->addColumn('particular', function ($row) {
                    $particular = $row->particular->name;

                    return $particular;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('product-types.edit', $row->id);
                    $deleteUrl = route('product-types.destroy', $row->id);

                    $btn = '<a href="' . $editUrl . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="' . $deleteUrl . '" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.product-types.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.product-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductTypeRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $product_type = ProductType::create($validatedData);

            return response()->json([
                'message' => 'ProductType created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create product-type',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product_type = ProductType::findOrFail($id);
        return view('pages.product-types.edit', compact('product_type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductTypeRequest $request, $id)
    {
        try {
            $product_type = ProductType::findOrFail($id);

            $validatedData = $request->validated();

            $product_type->update($validatedData);

            return response()->json([
                'message' => 'ProductType updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product-type',
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
            $product_type = ProductType::findOrFail($id);
            $product_type->delete();

            return response()->json(['message' => 'ProductType deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product-type', 'error' => $e->getMessage()], 500);
        }
    }

    public function getParticulars($materialId)
    {
        $particulars = Material::with('particular')->where('id', $materialId)->get();
        return response()->json($particulars);
    }

}
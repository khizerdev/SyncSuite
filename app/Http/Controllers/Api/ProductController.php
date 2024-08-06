<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use Dotenv\Exception\ValidationException;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        try {

            $validatedData = $request->validated();

            Product::create($validatedData);

            return response()->json([
                'message' => 'Product created successfully',
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
}
<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\ProductType\UpdateProductTypeRequest;
use App\Http\Requests\ProductType\StoreProductTypeRequest;
use App\Models\ProductType;
use Dotenv\Exception\ValidationException;

class ProductTypeController extends Controller
{
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
                'errors' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to create product-type',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

}
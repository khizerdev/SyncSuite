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

        // Get the latest product to determine the next serial number
        $latestProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $latestProduct ? $latestProduct->id + 1 : 1;

        // Format the serial number (PL-P001, PL-P002, etc.)
        $serialNo = 'PL-P' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Add the serial number to the validated data
        $validatedData['serial_no'] = $serialNo;

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product created successfully',
            'serial_no' => $serialNo,
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to create product',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
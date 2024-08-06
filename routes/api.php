<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\InwardGeneralController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ResourceController;

Route::get('/products/count', [ProductController::class, 'count']);
Route::get('/departments', [ResourceController::class, 'getDepartments']);
Route::get('/product-types', [ResourceController::class, 'getProductTypes']);
Route::get('/particulars', [ResourceController::class, 'getParticulars']);

Route::get('/materials', [ResourceController::class, 'getMaterials']);
Route::get('/particular-materials/{id}', [ResourceController::class, 'getParticularMaterials']);

Route::post('/inward-general/store', [InwardGeneralController::class, 'store']);
Route::put('/inward-general/{id}', [InwardGeneralController::class, 'update']);

Route::post('/products/store', [ApiProductController::class, 'store']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\InwardGeneralController;

Route::get('/products/count', [ProductController::class, 'count']);

Route::post('/inward-general/store', [InwardGeneralController::class, 'store']);
Route::put('/inward-general/{id}', [InwardGeneralController::class, 'update']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\InwardGeneralController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\GazetteController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ParticularController;
use App\Http\Controllers\SaleOrderController;

Route::get('/products/count', [ProductController::class, 'count']);
Route::get('/departments', [ResourceController::class, 'getDepartments']);
Route::get('/product-types', [ResourceController::class, 'getProductTypes']);
Route::get('/particulars', [ResourceController::class, 'getParticulars']);

Route::get('/materials', [ResourceController::class, 'getMaterials']);
Route::get('/particular-materials/{id}', [ResourceController::class, 'getParticularMaterials']);

Route::post('/inward-general/store', [InwardGeneralController::class, 'store']);
Route::put('/inward-general/{id}', [InwardGeneralController::class, 'update']);

Route::post('/products/store', [ApiProductController::class, 'store']);
Route::post('/productsType/store', [ProductTypeController::class, 'store']);

Route::post('/materials/store', [MaterialController::class, 'store']);
Route::post('/particulars/store', [ParticularController::class, 'store']);

Route::apiResource('shifts', ShiftController::class)->only([
    'store','update'
]);

Route::get('/gazette-holidays', [GazetteController::class, 'index'])->name('gazette-holidays.index');
Route::get('/gazette-holidays', [GazetteController::class, 'getHolidays']);
Route::post('/gazette-holidays', [GazetteController::class, 'store']);
Route::get('/gazette-holidays/check/{year}/{month}', [GazetteController::class, 'checkMonthlyHolidays']);

Route::get('/sale-orders/search', [SaleOrderController::class, 'search'])->name('sale-orders.search');

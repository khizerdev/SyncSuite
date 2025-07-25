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
use App\Http\Controllers\ShiftTransferController;
use App\Models\DailyProduction;
use App\Models\ThanIssueItem;

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

Route::prefix('api')->group(function () {
    Route::get('employees/by-department/{departmentId}', [ShiftTransferController::class, 'getEmployeesByDepartment']);
    Route::get('shift-transfers/data', [ShiftTransferController::class, 'getShiftTransfersData']);
});

Route::get('/gazette-holidays', [GazetteController::class, 'index'])->name('gazette-holidays.index');
Route::get('/gazette-holidays', [GazetteController::class, 'getHolidays']);
Route::post('/gazette-holidays', [GazetteController::class, 'store']);
Route::get('/gazette-holidays/check/{year}/{month}', [GazetteController::class, 'checkMonthlyHolidays']);

Route::get('/sale-orders/search', [SaleOrderController::class, 'search'])->name('sale-orders.search');

Route::get('/get-previous-stitch/{machineId}', function ($machineId) {
    $previousRecord = DailyProduction::where('machine_id', $machineId)
        ->latest()
        ->first();
    
    return response()->json([
        'previous_stitch' => $previousRecord ? $previousRecord->actual_stitch : 0
    ]);
});

Route::get('/daily-productions/search', function(Request $request) {
    $query = $request->input('q');

    $productions = DailyProduction::with([
        'items' => function($q) use ($query) {
            $q->where('than_qty', '>', 0)
              ->with(['saleOrderItem' => function($q) {
                  $q->with(['design', 'color', 'saleOrder']);
              }])
              ->whereHas('saleOrder', function($q) use ($query) {
                  $q->where('sale_no', 'like', "%$query%");
              });
        },
        'machine',
        'shift'
    ])
    ->whereHas('items.saleOrder', function($q) use ($query) {
        $q->where('sale_no', 'like', "%$query%");
    })
    ->get()
    ->map(function($production) {
        return [
            'id' => $production->id,
            'date' => $production->date,
            'machine' => $production->machine,
            'shift' => $production->shift,
            'items' => $production->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'sale_order_id' => $item->sale_order_id,
                    'than_qty' => $item->than_qty,
                    'design' => $item->saleOrderItem->design ?? null,
                    'color' => $item->saleOrderItem->color ?? null,
                    'saleOrder' => $item->saleOrderItem->saleOrder ?? null,
                    'sale_order_no' => $item->saleOrder->sale_no ?? null
                ];
            })
        ];
    });

    return response()->json($productions);
});

Route::get('/then-issue/search', function(Request $request) {
    $searchTerm = $request->input('search');
    
    $items = ThanIssueItem::query()
        ->where('than_issue_items.serial_no', 'LIKE', "%{$searchTerm}%")
        ->orWhereHas('dailyProductionItem.saleOrder', function($query) use ($searchTerm) {
            $query->where('sale_orders.sale_no', 'LIKE', "%{$searchTerm}%")
                  ->where('sale_orders.order_status', 'open');
        })
        ->with(['dailyProductionItem.saleOrder'])
        ->get();
        
    return response()->json($items);
});

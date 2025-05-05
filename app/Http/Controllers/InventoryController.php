<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\PurchaseOrderItems;
use App\Models\Product;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //  public static function middleware(): array
    //  {
    //      return [
    //          'permission:list-department|create-department|edit-department|delete-department' => ['only' => ['index', 'store']],
    //          'permission:create-department' => ['only' => ['create', 'store']],
    //          'permission:edit-department' => ['only' => ['edit', 'update']],
    //          'permission:delete-department' => ['only' => ['destroy']],
    //      ];
    //  }

     public function index(Request $request)
     {
         if ($request->ajax()) {
        $data = Product::with(['purchaseOrderItems'])
                ->select('products.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('total_in', function($row) {
                return $row->purchaseOrderItems->sum('qty');
            })
            ->addColumn('total_out', function($row) {
                return 0; // Default to 0 since no sales
            })
            ->addColumn('current_stock', function($row) {
                return $row->purchaseOrderItems->sum('qty') - 0; // Current stock = total in - total out (0)
            })
            ->addColumn('action', function($row) {
                return '<a href="'.route('inventory.show', $row->id).'" class="btn btn-info btn-sm">View Details</a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
         return view('pages.inventory.index');
     }
     
     public function show($id, Request $request)
{
    $product = Product::findOrFail($id);
    
    if ($request->ajax()) {
        // Get all purchase order items for this product
        $purchaseItems = PurchaseOrderItems::with(['purchase'])
            ->where('product_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->created_at,
                    'type' => 'IN',
                    'reference' => 'PO-' . $item->purchase->id,
                    'serial_no' => $item->purchase->serial_no,
                    'in' => $item->qty,
                    'out' => 0,
                    'created_at' => $item->created_at
                ];
            });

        // When you implement sales, add sale items here similarly
        // $saleItems = SaleItem::with(['saleOrder'])... 

        // Combine all transactions and sort by date
        $allTransactions = collect($purchaseItems)/*->merge($saleItems)*/
            ->sortBy('created_at');

        // Calculate running balance
        $runningBalance = 0;
        $ledgerEntries = $allTransactions->map(function ($item) use (&$runningBalance) {
            $runningBalance += $item['in'] - $item['out'];
            $item['balance'] = $runningBalance;
            return $item;
        });

        return DataTables::of($ledgerEntries)
            ->addIndexColumn()
            ->addColumn('type_badge', function($row) {
                $badgeClass = $row['type'] == 'IN' ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $badgeClass . '">' . $row['type'] . '</span>';
            })
            ->addColumn('date_formatted', function($row) {
                return $row['date']->format('Y-m-d H:i');
            })
            ->rawColumns(['type_badge'])
            ->toJson();
    }

    return view('pages.inventory.show', compact('product'));
}

    
    
}
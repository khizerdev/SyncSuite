<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\PurchaseOrderItems;
use App\Models\Product;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

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
                    return $row->qty + $row->purchaseOrderItems->sum('qty');
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
        $product = Product::with('departments')->findOrFail($id);
        
        if ($request->ajax()) {
            // Check if this is a request for department inventory
            if ($request->has('department_inventory')) {
                return DataTables::of($product->departments)
                    ->addIndexColumn()
                    ->addColumn('department_name', function($department) {
                        return $department->name;
                    })
                    ->addColumn('quantity', function($department) {
                        return $department->pivot->quantity;
                    })
                    ->toJson();
            }
    
      
// Get the product to access the opening quantity
$product = Product::find($id);
$openingQty = $product ? $product->qty : 0;

// Create opening balance entry if there's an opening quantity
$openingEntry = [];
if ($openingQty > 0) {
    $openingEntry = [[
        'date' => $product->created_at ?? now(),
        'type' => 'OPENING',
        'reference' => 'Opening Balance',
        'serial_no' => '-',
        'in' => $openingQty,
        'out' => 0,
        'created_at' => $product->created_at ?? now()
    ]];
}

// Original ledger functionality
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

// Combine opening balance with all transactions and sort by date
$allTransactions = collect($openingEntry)
    ->merge($purchaseItems)
    ->sortBy('created_at');

// Calculate running balance starting with opening balance
$runningBalance = 0;
$ledgerEntries = $allTransactions->map(function ($item) use (&$runningBalance) {
    $runningBalance += $item['in'] - $item['out'];
    $item['balance'] = $runningBalance;
    return $item;
});

return DataTables::of($ledgerEntries)
    ->addIndexColumn()
    ->addColumn('type_badge', function($row) {
        $badgeClass = match($row['type']) {
            'IN' => 'bg-success',
            'OPENING' => 'bg-info',
            default => 'bg-danger'
        };
        return '<span class="badge ' . $badgeClass . '">' . $row['type'] . '</span>';
    })
    ->addColumn('date_formatted', function($row) {
        return $row['date']->format('Y-m-d H:i');
    })
    ->rawColumns(['type_badge'])
    ->toJson();
        }
    
        $departments = Department::all();
        return view('pages.inventory.show', compact('product', 'departments'));
    }
    
    public function bulk_transfer(){
        return view('pages.inventory.transfer');
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
        'from_department' => 'required|exists:departments,id',
        'to_department' => 'required|exists:departments,id|different:from_department',
        'transfers' => 'required|array|min:1',
        'transfers.*.product_id' => 'required|exists:products,id',
        'transfers.*.quantity' => 'required|integer|min:1'
    ]);
    
        // Force transfers to come only from Main department
        $mainDepartment = Department::where('name', 'Main')->firstOrFail();
        if ($request->from_department != $mainDepartment->id) {
            return back()->with('error', 'Transfers can only be initiated from Main department');
        }
    
        DB::beginTransaction();
        try {
            foreach ($request->transfers as $transfer) {
                $product = Product::findOrFail($transfer['product_id']);
                
                // Check Main department stock
                $mainStock = $product->departments()
                    ->where('department_id', $mainDepartment->id)
                    ->first();
                
                if (!$mainStock || $mainStock->pivot->quantity < $transfer['quantity']) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$product->name} in Main department");
                }
    
                // Deduct from Main
                $product->departments()->updateExistingPivot(
                    $mainDepartment->id,
                    ['quantity' => DB::raw("quantity - {$transfer['quantity']}")]
                );
    
                // Add to target department
                $targetDepartment = $product->departments()
                    ->where('department_id', $request->to_department)
                    ->first();
                
                if ($targetDepartment) {
                    $product->departments()->updateExistingPivot(
                        $request->to_department,
                        ['quantity' => DB::raw("quantity + {$transfer['quantity']}")]
                    );
                } else {
                    $product->departments()->attach($request->to_department, [
                        'quantity' => $transfer['quantity']
                    ]);
                }
            }
    
            DB::commit();
            return back()->with('success', 'Stock transferred successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    
    
}
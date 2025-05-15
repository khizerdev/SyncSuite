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

        // Combine all transactions and sort by date
        $allTransactions = collect($purchaseItems)
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

    $departments = Department::all();
    return view('pages.inventory.show', compact('product', 'departments'));
}

public function transfer(Request $request, $productId)
    {
        $request->validate([
            'from_department' => 'required|exists:departments,id',
            'to_department' => 'required|exists:departments,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $product = Product::findOrFail($productId);
        
        // Check if from department has enough quantity
        $fromDepartment = $product->departments()
                                ->where('department_id', $request->from_department)
                                ->first();
                                
        if (!$fromDepartment || $fromDepartment->pivot->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient quantity in source department');
        }
        
        // Decrease from source department
        $product->departments()->updateExistingPivot(
            $request->from_department,
            ['quantity' => $fromDepartment->pivot->quantity - $request->quantity]
        );
        
        // Increase in target department
        $toDepartment = $product->departments()
                              ->where('department_id', $request->to_department)
                              ->first();
                              
        if ($toDepartment) {
            $product->departments()->updateExistingPivot(
                $request->to_department,
                ['quantity' => $toDepartment->pivot->quantity + $request->quantity]
            );
        } else {
            $product->departments()->attach($request->to_department, [
                'quantity' => $request->quantity
            ]);
        }
        
        return back()->with('success', 'Stock transferred successfully');
    }

    
    
}
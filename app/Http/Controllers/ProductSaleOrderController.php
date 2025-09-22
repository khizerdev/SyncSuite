<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\User;
use App\Services\AccountService;
use App\Models\ProductSaleOrderItem;
use App\Models\ProductSaleOrder;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class ProductSaleOrderController extends Controller
{
    public function create()
{
    $modules = ProductSaleOrder::all();
    $products = Product::all();
    $customers = Customer::all();
    return view("pages.product_sale_orders.create", compact("modules", "products", "customers"));
}

public function store(Request $request)
{
    $request->validate(["date" => "required"]);
    $date = Carbon::createFromFormat("Y-m-d", $request->date);
    $last = ProductSaleOrder::whereYear("date", $date->format("Y"))
        ->whereMonth("date", $date->format("m"))
        ->orderBy("serial", "DESC")
        ->first();
    
    $serial_no = "SO-" . $date->format("ym") . str_pad($last ? $last->serial + 1 : 1, 3, "0", STR_PAD_LEFT);
    DB::beginTransaction();
    try {
        // 1. Create the product sale order
        $saleOrder = ProductSaleOrder::create([
            "customer_id" => $request->customer_id,
            "serial_no" => $serial_no,
            "serial" => $last ? $last->serial + 1 : 1,
            "date" => $request->date,
            "descr" => $request->descr
        ]);
        
        // 2. Get the Main department
        $mainDepartment = Department::where('name', 'Main')->firstOrFail();
        
        // 3. Process items
        if ($request->has("items")) {
            foreach ($request->items as $item) {
                // A. Record sale order item
                ProductSaleOrderItem::create([
                    "product_id" => $item["id"],
                    "product_sale_order_id" => $saleOrder->id,
                    "qty" => $item["qty"],
                    "rate" => $item["rate"],
                    "dispatched_through" => $item["dispatched_through"]
                ]);
                
                // B. Update Main department inventory (deduct quantity)
                DB::table('inventory_department')->updateOrInsert(
                    [
                        'product_id' => $item["id"],
                        'department_id' => $mainDepartment->id
                    ],
                    [
                        'quantity' => DB::raw("quantity - {$item['qty']}"),
                        'updated_at' => now()
                    ]
                );
            }
        }
        DB::commit();
        return redirect()->route("product_sale_orders.index")->with("success", "Sale order recorded!");
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

public function index()
{
    $product_sale_orders = ProductSaleOrder::with(['customer', 'items.product'])->latest()->get();
    return view('pages.product_sale_orders.index', compact('product_sale_orders'));
}

public function edit($id)
{
    $product_sale_order = ProductSaleOrder::with(['items.product'])->findOrFail($id);
    $products = Product::all();
    $customers = Customer::all();
    
    return view('pages.product_sale_orders.edit', compact('product_sale_order', 'products', 'customers'));
}

public function update(Request $request, $id)
{
    $request->validate([
        "date" => "required",
        "customer_id" => "required",
        "payment_method" => "required|in:cash,loan",
        "status" => "required|in:pending,completed,cancelled"
    ]);
    
    DB::beginTransaction();
    try {
        $saleOrder = ProductSaleOrder::findOrFail($id);
        
        // Restore inventory if status is changing from completed
        if ($saleOrder->status == 'completed' && $request->status != 'completed') {
            $mainDepartment = Department::where('name', 'Main')->firstOrFail();
            foreach ($saleOrder->items as $item) {
                DB::table('inventory_department')->updateOrInsert(
                    [
                        'product_id' => $item->product_id,
                        'department_id' => $mainDepartment->id
                    ],
                    [
                        'quantity' => DB::raw("quantity + {$item->qty}"),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        // Update the sale order
        $saleOrder->update([
            "customer_id" => $request->customer_id,
            "date" => $request->date,
            "descr" => $request->descr,
            "payment_method" => $request->payment_method,
            "status" => $request->status
        ]);
        
        // Delete existing items
        $saleOrder->items()->delete();
        
        // Process new items
        $mainDepartment = Department::where('name', 'Main')->firstOrFail();
        if ($request->has("items")) {
            foreach ($request->items as $item) {
                // Record sale order item
                ProductSaleOrderItem::create([
                    "product_id" => $item["id"],
                    "product_sale_order_id" => $saleOrder->id,
                    "qty" => $item["qty"],
                    "rate" => $item["rate"],
                    "dispatched_through" => $item["dispatched_through"]
                ]);
                
                // Update inventory if order is completed
                if ($request->status == 'completed') {
                    DB::table('inventory_department')->updateOrInsert(
                        [
                            'product_id' => $item["id"],
                            'department_id' => $mainDepartment->id
                        ],
                        [
                            'quantity' => DB::raw("quantity - {$item['qty']}"),
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
        
        DB::commit();
        return redirect()->route("product_sale_orders.index")->with("success", "Sale order updated!");
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

public function show($id)
{
    $product_sale_order = ProductSaleOrder::with(['customer', 'items.product'])->findOrFail($id);
    return view('pages.product_sale_orders.show', compact('product_sale_order'));
}

public function destroy($id)
{
    DB::beginTransaction();
    try {
        $saleOrder = ProductSaleOrder::with('items')->findOrFail($id);
        
        // Restore inventory if order was completed
        if ($saleOrder->status == 'completed') {
            $mainDepartment = Department::where('name', 'Main')->firstOrFail();
            foreach ($saleOrder->items as $item) {
                DB::table('inventory_department')->updateOrInsert(
                    [
                        'product_id' => $item->product_id,
                        'department_id' => $mainDepartment->id
                    ],
                    [
                        'quantity' => DB::raw("quantity + {$item->qty}"),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        $saleOrder->items()->delete();
        $saleOrder->delete();
        
        DB::commit();
        return redirect()->route("product_sale_orders.index")->with("success", "Sale order deleted!");
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
}
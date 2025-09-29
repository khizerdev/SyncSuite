<?php

namespace App\Http\Controllers;

use App\Models\ReceiveLoan;
use App\Models\ReceiveLoanItem;
use App\Models\ProductSaleOrder;
use App\Models\Product;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ReceiveLoanController extends Controller
{
    public function create()
    {
        // Get sale orders that are not yet received as loans and have payment method as 'loan'
        $saleOrders = ProductSaleOrder::where('payment_method', 'loan')
            ->whereDoesntHave('receiveLoan')
            ->with('customer')
            ->get();
            
        $products = Product::with('particular')->get();

        return view('pages.receive_loans.create', compact('saleOrders', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "date" => "required",
            "product_sale_order_id" => "required|exists:product_sale_orders,id"
        ]);

        $date = Carbon::createFromFormat("Y-m-d", $request->date);
        
        DB::beginTransaction();
        try {
            // 1. Create the receive loan record
            $receiveLoan = ReceiveLoan::create([
                "product_sale_order_id" => $request->product_sale_order_id,
                "date" => $request->date,
                "descr" => $request->descr
            ]);
            
            // 2. Get the Main department
            $mainDepartment = Department::where('name', 'Main')->firstOrFail();
            
            // 3. Process items
            if ($request->has("items")) {
                foreach ($request->items as $item) {
                    // A. Record receive loan item
                    ReceiveLoanItem::create([
                        "product_id" => $item["id"],
                        "receive_loan_id" => $receiveLoan->id,
                        "qty" => $item["qty"],
                        "rate" => $item["rate"],
                        "dispatched_through" => $item["dispatched_through"]
                    ]);
                    
                    // B. Update Main department inventory (add quantity back)
                    DB::table('inventory_department')->updateOrInsert(
                        [
                            'product_id' => $item["id"],
                            'department_id' => $mainDepartment->id
                        ],
                        [
                            'quantity' => DB::raw("quantity + {$item['qty']}"),
                            'updated_at' => now()
                        ]
                    );
                }
            }
            
            DB::commit();
            return redirect()->route("receive_loans.index")->with("success", "Loan received successfully!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function index()
{
    $receiveLoans = ReceiveLoan::with(['productSaleOrder.customer', 'items.product'])
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(10); // This returns a LengthAwarePaginator instance

    return view('pages.receive_loans.index', compact('receiveLoans'));
}
}
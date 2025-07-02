<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use App\Models\Customer;
use App\Models\SaleOrderItem;
use App\Models\DailyProductionItem;
use Illuminate\Http\Request;
use DataTables;

class SaleOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SaleOrder::with('customer')->get();
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $editUrl = route('sale-orders.edit', $row->id);

                $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>';

                
                $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                
                // $btn = $edit.$delete;
                return $btn;
            
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.sale-orders.index');
    }

    public function create()
    {
        $customers = Customer::all();
        return view('pages.sale-orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_status' => 'required|in:open,hold,cleared',
            'order_reference' => 'required|string|max:255',
            'advance_payment' => 'required|numeric',
            'delivery_date' => 'required|date',
            'payment_terms' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
    
        // Get the latest sale order to determine the next number
        $latestOrder = SaleOrder::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        
        if ($latestOrder && preg_match('/PL-SB-(\d+)/', $latestOrder->sale_no, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        // Format the number with leading zeros
        $saleNo = 'PL-SB-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
        // Create the sale order with all fields including the generated sale_no
        $saleOrder = SaleOrder::create(array_merge(
            $request->only('customer_id', 'order_status', 'order_reference', 'advance_payment', 'delivery_date', 'payment_terms', 'description'),
            ['sale_no' => $saleNo]
        ));
        
        foreach ($request->design_name as $index => $designName) {
            SaleOrderItem::create([
                'sale_order_id'   => $saleOrder->id,
                'design_id'  => $request->design_name[$index],
                'color_id'       => $request->colour_id[$index],
                'qty'       => $request->qty[$index],
                'lace_qty'       => $request->lace_qty[$index],
                'rate'       => $request->rate[$index],
                'stitch'       => $request->stitch[$index],
                'stitch_rate'       => $request->stitch_rate[$index],
                'calculate_stitch'       => $request->calculate_stitch[$index],
                'length_factor'       => $request->length_factor[$index],
                'amount'       => $request->amount[$index],
            ]);
        }

        return redirect()->route('sale-orders.index')->with('success', 'Created successfully.');
    }

    public function edit(SaleOrder $saleOrder)
    {
        $customers = Customer::all();
        return view('pages.sale-orders.edit', compact('saleOrder', 'customers'));
    }

    public function update(Request $request, SaleOrder $saleOrder)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_status' => 'required|in:open,hold,cleared',
            'order_reference' => 'required|string|max:255',
            'advance_payment' => 'required|numeric',
            'delivery_date' => 'required|date',
            'payment_terms' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Update the sale order
        $saleOrder->update($request->only('customer_id','order_status','order_reference','advance_payment','delivery_date','payment_terms','description'));

        // Delete all existing items
        $saleOrder->items()->delete();

        // Create new items
        foreach ($request->design_name as $index => $designName) {
            SaleOrderItem::create([
                'sale_order_id'   => $saleOrder->id,
                'design_id'  => $request->design_name[$index],
                'color_id'       => $request->colour_id[$index],
                'qty'       => $request->qty[$index],
                'lace_qty'       => $request->lace_qty[$index],
                'rate'       => $request->rate[$index],
                'stitch'       => $request->stitch[$index],
                'stitch_rate'       => $request->stitch_rate[$index],
                'calculate_stitch'       => $request->calculate_stitch[$index],
                'length_factor'       => $request->length_factor[$index],
                'amount'       => $request->amount[$index],
            ]);
        }

        return redirect()->route('sale-orders.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $item = SaleOrder::findOrFail($id);
           
            $item->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }

    // In your API controller
public function search(Request $request)
{
    $query = $request->input('q');
    
    $saleOrders = SaleOrder::with(['customer', 'items.design', 'items.color'])
        ->where(function($q) use ($query) {
            $q->where('id', 'like', "%{$query}%")
              ->orWhereHas('items.design', function($q) use ($query) {
                  $q->where('design_code', 'like', "%{$query}%");
              });
        })
        ->get()
        ->map(function($order) {
            $order->items->each(function($item) {
                // Calculate used quantities
                $used = DailyProductionItem::where('sale_order_item_id', $item->id)
                    ->selectRaw('SUM(lace_qty) as total_lace_qty, SUM(than_qty) as total_than_qty')
                    ->first();
                
                $item->used_lace_qty = $used->total_lace_qty ?? 0;
                $item->used_qty = $used->total_than_qty ?? 0;
            });
            return $order;
        });
    
    return response()->json($saleOrders);
}

}
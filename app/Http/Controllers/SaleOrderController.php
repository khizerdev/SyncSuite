<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use App\Models\Customer;
use App\Models\SaleOrderItem;
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

        $saleOrder = SaleOrder::create($request->only('customer_id','order_status','order_reference','advance_payment','delivery_date','payment_terms','description'));
        foreach ($request->design_name as $index => $designName) {
            SaleOrderItem::create([
                'sale_order_id'   => $saleOrder->id,
                'design_id'  => $request->design_name[$index],
                'colour'       => $request->colour[$index],
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
                'colour'       => $request->colour[$index],
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

    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        
        $saleOrders = SaleOrder::with(['customer', 'items.design'])
            ->where(function($query) use ($searchTerm) {
                // Search by sale order ID
                $query->where('id', 'like', "%{$searchTerm}%")
                    // Or search by fabric design code in items
                    ->orWhereHas('items.design', function($q) use ($searchTerm) {
                        $q->where('design_code', 'like', "%{$searchTerm}%");
                    });
            })
            ->withCount('items')
            ->limit(10)
            ->get();
        
        return response()->json($saleOrders);
    }

}
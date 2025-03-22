<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use App\Models\Customer;
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

        SaleOrder::create($request->all());

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

        $saleOrder->update($request->all());

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
}
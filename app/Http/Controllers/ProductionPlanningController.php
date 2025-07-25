<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlanning;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use DataTables;

class ProductionPlanningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductionPlanning::with('machine')->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editUrl = route('production-plannings.edit', $row->id);
                    
                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    
                    // $btn = $edit;
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('pages.production_plannings.index');
    }

    public function create()
    {
        return view('pages.production_plannings.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'machine_id' => 'required|exists:machines,id',
        'sale_order_id' => 'required|exists:sale_orders,id',
        'selected_item' => 'required|exists:sale_order_items,id',
        'items' => 'required|array',
        'items.*.planned_qty' => 'required|integer|min:1',
        'items.*.planned_lace_qty' => 'required|integer|min:0',
    ]);

    // Get the selected item ID from either selected_item or sale_order_item_id
    $itemId = $request->selected_item ?? $request->sale_order_item_id;
    
    // Get the sale order item to validate quantities
    $saleOrderItem = SaleOrderItem::findOrFail($itemId);

    // Get the planned quantities from the items array
    $plannedQty = $request->items[$itemId]['planned_qty'];
    $plannedLaceQty = $request->items[$itemId]['planned_lace_qty'];

    // Validate against original quantities
    if ($plannedQty > $saleOrderItem->qty) {
        return back()->withErrors(['planned_qty' => 'Planned quantity cannot exceed original quantity ('.$saleOrderItem->qty.')']);
    }

    if ($plannedLaceQty > $saleOrderItem->lace_qty) {
        return back()->withErrors(['planned_lace_qty' => 'Planned lace quantity cannot exceed original quantity ('.$saleOrderItem->lace_qty.')']);
    }

    // Create the production planning record
    ProductionPlanning::create([
        'date' => $request->date,
        'machine_id' => $request->machine_id,
        'sale_order_id' => $request->sale_order_id,
        'planned_qty' => $plannedQty,
        'planned_lace_qty' => $plannedLaceQty,
    ]);

    return redirect()->route('production-plannings.index')
                     ->with('success', 'Production planning created successfully.');
}

    public function edit(ProductionPlanning $productionPlanning)
    {
        $productionPlanning->load([
            'saleOrder.customer',
            'saleOrder.items.design',
            'saleOrder.items.color'
        ]);
        
        return view('pages.production_plannings.edit', compact('productionPlanning'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $productionPlanning = ProductionPlanning::find($id);
        $productionPlanning->update($request->only('date','machine_id','sale_order_id'));

        return redirect()->route('production-plannings.index')
                         ->with('success', 'Production Planning updated successfully');
    }

    public function destroy($id)
    {
        ProductionPlanning::find($id)->delete();
        return response()->json(['success' => 'Production Planning deleted successfully.']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlanning;
use Illuminate\Http\Request;
use DataTables;

class ProductionPlanningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductionPlanning::latest()->get();
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
        ]);

        ProductionPlanning::create([
            'date' => $request->date,
            'machine_id' => $request->machine_id,
            'sale_order_id' => $request->saleorder_id,
        ]);

        return redirect()->route('production-plannings.index')
                         ->with('success', 'Created successfully.');
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
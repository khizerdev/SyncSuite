<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Department;
use App\Models\ThanSupply;
use App\Models\ThanSupplyItem;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('department', 'batchItems.thanSupplyItem')->get();
        return view('pages.batches.index', compact('batches'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('pages.batches.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|unique:batches',
            'department_id' => 'required|exists:departments,id',
            'than_supply_item_ids' => 'required|array',
            'than_supply_item_ids.*' => 'exists:than_supply_items,id'
        ]);

        $batch = Batch::create($request->only('reference_number', 'department_id'));

        foreach ($request->than_supply_item_ids as $itemId) {
            $batch->batchItems()->create(['than_supply_item_id' => $itemId]);
        }

        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    public function getThanSupplies(Department $department)
    {
        $thanSupplies = $department->thanSupplies;
        return response()->json($thanSupplies);
    }

    public function getThanSupplyItems(ThanSupply $thanSupply)
    {
        $thanSupplyItems = $thanSupply->thanSupplyItems;
        return response()->json($thanSupplyItems);
    }

    public function getThanSuppliesApi($departmentId)
    {
        $thanSupplies = ThanSupply::where('department_id', $departmentId)->get();
        return response()->json($thanSupplies);
    }

    public function getThanSupplyItemsApi($thanSupplyId)
    {
        $thanSupplyItems = ThanSupplyItem::where('than_supply_id', $thanSupplyId)->get();
        return response()->json($thanSupplyItems);
    }
}
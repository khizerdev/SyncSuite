<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Department;
use App\Models\ThanSupply;
use App\Models\ThanSupplyItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BatchController extends Controller
{
   
    
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Batch::latest()->get();
            return DataTables::of($data)
            ->addColumn('department', function ($row) {
                return $row->department->name;
            })
               ->addColumn('action', function($row){
                   $editUrl = route('batches.edit', $row->id);
                   $deleteUrl = route('batches.destroy', $row->id);

                   $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                   $btn .= '<button onclick="deleteRecord(\'' . $row->id . '\', \'/batches/\', \'GET\')" class="delete btn btn-danger btn-sm mr-2">Delete</button>';
                   return $btn;
               })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.batches.index');
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

    // Check if all selected items belong to the specified department
    $invalidItems = ThanSupplyItem::whereIn('id', $request->than_supply_item_ids)
        ->whereHas('thanSupply', function($query) use ($request) {
            $query->where('department_id', '!=', $request->department_id);
        })
        ->exists();

    if ($invalidItems) {
        return back()->withErrors(['than_supply_item_ids' => 'One or more selected items do not belong to the specified department.'])
                     ->withInput();
    }

    $batch = Batch::create($request->only('reference_number', 'department_id'));

    foreach ($request->than_supply_item_ids as $itemId) {
        $batch->batchItems()->create(['than_supply_item_id' => $itemId]);
        
        // Optionally update the status of the than supply item to indicate it's used in a batch
        // ThanSupplyItem::where('id', $itemId)->update(['status' => 'used']);
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
    
    public function destroy($id)
    {
        try {
            $branch = Batch::findOrFail($id);
            $branch->delete();
    
            return response()->json(['message' => 'Batch deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete batch', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getThanSupplyItemsByDepartment($departmentId)
{
    $thanSupplyItems = ThanSupplyItem::whereHas('thanSupply', function($query) use ($departmentId) {
        $query->where('department_id', $departmentId);
    })->with('thanSupply')->get();
    
    return response()->json($thanSupplyItems->map(function($item) {
        return [
            'id' => $item->id,
            'serial_no' => $item->serial_no,
            'description' => $item->description,
            'than_supply_serial_no' => $item->thanSupply->serial_no
        ];
    }));
}
}
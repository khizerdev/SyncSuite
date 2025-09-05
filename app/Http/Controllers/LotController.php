<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\InventoryDepartment;
use App\Models\Lot;
use App\Models\Shift;
use Illuminate\Http\Request;
use DataTables;

class LotController extends Controller
{
    public function index(Request $request)
     {
        if ($request->ajax()) {
             $data = Lot::with('batch','shift')->latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('loans.edit', $row->id);
                    $deleteUrl = route('loans.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        
        return view('pages.lots.index');
     }

    
    public function create()
    {
        $batches = Batch::with('department')->get();
        $shiftMachines = Shift::all();
        
        return view('pages.lots.create', compact('batches', 'shiftMachines'));
    }
    
    public function getBatchDetails($batchId)
    {
        try {
            $batch = Batch::with([
                'department',
                'batchItems.thanSupplyItem' => function($query) {
                    $query->select('id', 'serial_no'); // Add other fields you need
                }
            ])->findOrFail($batchId);
            
            return response()->json([
                'success' => true,
                'batch' => [
                    'id' => $batch->id,
                    'name' => $batch->name,
                    'reference_number' => $batch->reference_number,
                    'department' => $batch->department,
                    'batch_items' => $batch->batchItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'than_supply_item_id' => $item->than_supply_item_id,
                            'serial_no' => $item->thanSupplyItem->serial_no ?? 'N/A',
                            // Add other batch item fields you need
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ], 404);
        }
    }

    public function getProductsByDepartment(Request $request)
    {
        $departmentId = $request->department_id;
        
        $products = InventoryDepartment::with('product')
            ->where('department_id', $departmentId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'quantity' => $item->quantity
                ];
            });
            
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'steam_open' => 'nullable|date',
            'steam_closed' => 'nullable|date|after:steam_open',
            'temperature' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'total_dyeing_time' => 'nullable|integer',
            'running_time' => 'nullable|integer',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        // Calculate run time
        $startTime = new \DateTime($validated['start_time']);
        $endTime = new \DateTime($validated['end_time']);
        $runTime = $endTime->diff($startTime)->i; // Difference in minutes

        $lot = Lot::create([
            'batch_id' => $validated['batch_id'],
            'shift_id' => $validated['shift_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'run_time' => $runTime,
            'steam_open' => $validated['steam_open'] ?? null,
            'steam_closed' => $validated['steam_closed'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'total_dyeing_time' => $validated['total_dyeing_time'] ?? null,
            'running_time' => $validated['running_time'] ?? null,
        ]);

        // Attach products with quantities
        foreach ($validated['products'] as $product) {
            $lot->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return redirect()->route('lots.index')->with('success', 'Lot created successfully.');
    }
    
    public function destroy(Lot $lot)
{
    $lot->products()->detach();
    $lot->delete();
    
    return redirect()->route('lots.index')->with('success', 'Lot deleted successfully.');
}
}
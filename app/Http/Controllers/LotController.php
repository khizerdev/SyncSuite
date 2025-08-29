<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\InventoryDepartment;
use App\Models\Lot;
use App\Models\Shift;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index(Request $request)
    {
       
        
        $lots = Lot::all();
        $batches = Batch::all();
        
        return view('pages.lots.index', compact('lots', 'batches'));
    }
    
    public function create()
    {
        $batches = Batch::with('department')->get();
        $shiftMachines = Shift::all();
        
        return view('pages.lots.create', compact('batches', 'shiftMachines'));
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
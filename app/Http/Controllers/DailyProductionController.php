<?php

namespace App\Http\Controllers;

use App\Models\DailyProduction;
use App\Models\DailyProductionItem;
use App\Models\Machine;
use App\Models\Shift;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class DailyProductionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DailyProduction::with(['shift', 'machine'])->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('shift', function($row) {
                        return $row->shift->name;
                    })
                    ->addColumn('machine', function($row) {
                        return $row->machine->name;
                    })
                    ->addColumn('action', function($row){
                        $editUrl = route('daily-productions.edit', $row->id);
    
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('pages.daily-productions.index');
    }

    public function create()
    {
        $shifts = Shift::all();
        $machines = Machine::all();
        return view('pages.daily-productions.create', compact('shifts', 'machines'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'machine_id' => 'required|exists:machines,id',
            'current_stitch' => 'required|integer',
            'description' => 'nullable|string',
            'saleorders' => 'required|array',
            'saleorders.*.id' => 'required|exists:sale_orders,id',
            'saleorders.*.items' => 'required|array',
            'saleorders.*.items.*.sale_order_item_id' => 'required|exists:sale_order_items,id',
            'saleorders.*.items.*.needle' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Create the daily production record
            $dailyProduction = DailyProduction::create([
                'shift_id' => $validated['shift_id'],
                'date' => $validated['date'],
                'machine_id' => $validated['machine_id'],
                'current_stitch' => $validated['current_stitch'],
                'description' => $validated['description'],
            ]);

            // Create production items
            foreach ($validated['saleorders'] as $saleOrderData) {
                foreach ($saleOrderData['items'] as $item) {
                    DailyProductionItem::create([
                        'daily_production_id' => $dailyProduction->id,
                        'sale_order_id' => $saleOrderData['id'],
                        'sale_order_item_id' => $item['sale_order_item_id'],
                        'needle' => $item['needle'],
                    ]); 
                }
            }

            DB::commit();

            return redirect()->route('daily-productions.index')
                ->with('success', 'Daily production created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->withInput()
                ->with('error', 'Error creating daily production: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $dailyProduction = DailyProduction::with(['items.saleOrder', 'items.saleOrderItem'])
            ->findOrFail($id);
            
        // Load related data
        $shifts = \App\Models\Shift::all();
        $machines = \App\Models\Machine::all();
        
        // Format sale orders with items for JavaScript
        $dailyProduction->saleOrdersWithItems = $dailyProduction->items->groupBy('sale_order_id')->map(function ($items, $saleOrderId) {
            $saleOrder = $items->first()->saleOrder;
            return [
                'id' => $saleOrder->id,
                'customer' => ['name' => $saleOrder->customer->name],
                'delivery_date' => $saleOrder->delivery_date,
                'order_status' => $saleOrder->order_status,
                'items_count' => $items->count(),
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->sale_order_item_id,
                        'design' => ['design_code' => $item->saleOrderItem->design->design_code],
                        'color' => ['title' => $item->saleOrderItem->color->title],
                        'lace_qty' => $item->saleOrderItem->lace_qty,
                        'qty' => $item->saleOrderItem->qty,
                        'rate' => $item->saleOrderItem->rate,
                        'amount' => $item->saleOrderItem->amount,
                        'stitch' => $item->saleOrderItem->stitch,
                        'pivot' => ['needle' => $item->needle]
                    ];
                })->toArray()
            ];
        })->values()->toArray();

        return view('pages.daily-productions.edit', compact('dailyProduction', 'shifts', 'machines'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'machine_id' => 'required|exists:machines,id',
            // 'previous_stitch' => 'required|integer',
            'current_stitch' => 'required|integer',
            // 'actual_stitch' => 'required|integer',
            'description' => 'nullable|string',
            'saleorders' => 'required|array',
            'saleorders.*.id' => 'required|exists:sale_orders,id',
            'saleorders.*.items' => 'required|array',
            'saleorders.*.items.*.sale_order_item_id' => 'required|exists:sale_order_items,id',
            'saleorders.*.items.*.needle' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Update the daily production record
            $dailyProduction = DailyProduction::findOrFail($id);
            $dailyProduction->update([
                'shift_id' => $validated['shift_id'],
                'date' => $validated['date'],
                'machine_id' => $validated['machine_id'],
                // 'previous_stitch' => $validated['previous_stitch'],
                'current_stitch' => $validated['current_stitch'],
                // 'actual_stitch' => $validated['actual_stitch'],
                'description' => $validated['description'],
            ]);

            // First, remove all existing items
            $dailyProduction->items()->delete();

            // Then add the updated items
            foreach ($validated['saleorders'] as $saleOrderData) {
                foreach ($saleOrderData['items'] as $item) {
                    DailyProductionItem::create([
                        'daily_production_id' => $dailyProduction->id,
                        'sale_order_id' => $saleOrderData['id'],
                        'sale_order_item_id' => $item['sale_order_item_id'],
                        'needle' => $item['needle'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('daily-productions.index')
                ->with('success', 'Daily production updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating daily production: ' . $e->getMessage());
        }
    }

    public function destroy(DailyProduction $dailyProduction)
    {
        try {
            $dailyProduction->delete();

            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}
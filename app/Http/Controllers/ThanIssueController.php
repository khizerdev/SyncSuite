<?php

namespace App\Http\Controllers;

use App\Models\ThanIssue;
use App\Models\ProductGroup;
use App\Models\Department;
use App\Models\Party;
use App\Models\DailyProduction;
use App\Models\DailyProductionItem;
use App\Models\ThanIssueItem;
use App\Models\FabricMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class ThanIssueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
             $data = ThanIssue::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('than-issues.edit', $row->id);
                    $deleteUrl = route('than-issues.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        return view('pages.than-issues.index');
    }

    public function create()
    {
        $dailyProductions = DailyProduction::with(['items' => function($query) {
            $query->where('than_qty', '>', 0)
                  ->whereDoesntHave('thanIssueItems');
        }])->whereDate('date', today())->get();
        
        $productGroups = ProductGroup::all();
        $departments = Department::all();
        $parties = Party::all();
        
        $designs = FabricMeasurement::select('id', 'design_code')->get();
        
        return view('pages.than-issues.create', compact(
            'dailyProductions',
            'productGroups',
            'departments',
            'parties',
            'designs'
        ));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'product_group_id' => 'required|exists:product_groups,id',
        'production_search' => 'required|string',
        'lace_qty' => 'required|array',
        'lace_qty.*' => 'numeric|min:0',
        'weight' => 'required|array',
        'weight.*' => 'numeric|min:0',
        'designs.*' => 'required|array|min:1',
        'designs.*.*' => 'exists:fabric_measurements,id',
        'designs.*.*' => 'exists:fabric_measurements,id',
        'remarks' => 'nullable|string'
    ]);

    // Extract daily_production_item_ids and sequences from the lace_qty/weight keys
    $itemsData = [];
    foreach ($validated['lace_qty'] as $key => $value) {
        [$parentId, $sequence] = explode('-', $key);
        $itemsData[] = [
            'parentId' => $parentId,
            'sequence' => $sequence,
            'lace_qty' => $value,
            'weight' => $validated['weight'][$key],
            'designs' => $validated['designs'][$key] ?? []
        ];
    }

    // Generate than issue serial number
    $lastThanIssue = ThanIssue::orderBy('id', 'desc')->first();
    $thanSerialNumber = 'th-' . str_pad(($lastThanIssue ? $lastThanIssue->id + 1 : 1), 3, '0', STR_PAD_LEFT);
    
    DB::transaction(function () use ($validated, $itemsData) {
        
        // Create the main Than Issue
        $thanIssue = ThanIssue::create([
            'issue_date' => $validated['date'],
            'daily_production_id' => DailyProductionItem::find($itemsData[0]['parentId'])->daily_production_id,
            'product_group_id' => $validated['product_group_id'],
            'remarks' => $validated['remarks'] ?? null
        ]);
    
        // Create Than Issue Items and their designs
        foreach ($itemsData as $item) {
            $dailyItem = DailyProductionItem::with('saleOrderItem')->find($item['parentId']);
            
            $thanIssueItem = ThanIssueItem::create([
                'than_issue_id' => $thanIssue->id,
                'daily_production_item_id' => $item['parentId'],
                'product_group_id' => $validated['product_group_id'],
                'quantity' => 1,
                'lace_qty' => $item['lace_qty'],
                'weight' => $item['weight']
            ]);
    
           // Attach fabric measurements to the than issue item
if (!empty($item['designs'])) {
    $thanIssueItem->fabricMeasurements()->attach($item['designs']);
}
        }
        
    });


    return redirect()->route('than-issues.index')
        ->with('success', 'Than Issue created successfully.');
}

protected function generateItemSerial($prefix)
{
    $lastSerial = ThanIssueItem::where('serial_no', 'like', $prefix . '-%')
        ->orderBy('serial_no', 'desc')
        ->first();

    $serialNumber = 1;
    if ($lastSerial) {
        preg_match('/-(\d+)$/', $lastSerial->serial_no, $matches);
        $serialNumber = (int)$matches[1] + 1;
    }

    return $prefix . '-' . str_pad($serialNumber, 3, '0', STR_PAD_LEFT);
}

    public function show(ThanIssue $thanIssue)
    {
        return view('pages.than-issues.show', compact('thanIssue'));
    }

    public function edit(ThanIssue $thanIssue)
{
    $productGroups = ProductGroup::all();
    $designs = FabricMeasurement::all();
    
    // Load the than issue with its items and related data
    $thanIssue->load([
        'items.dailyProductionItem.design',
        'items.dailyProductionItem.color',
        'items.dailyProductionItem.saleOrderItem',
        'items.fabricMeasurements'
    ]);
    
    $thanIssueItems = $thanIssue->items;

    return view('pages.than-issues.edit', compact(
        'thanIssue', 
        'productGroups', 
        'designs',
        'thanIssueItems'
    ));
}

public function update(Request $request, ThanIssue $thanIssue)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'product_group_id' => 'required|exists:product_groups,id',
        'production_search' => 'nullable|string',
        'lace_qty' => 'required|array',
        'lace_qty.*' => 'numeric|min:0',
        'weight' => 'required|array',
        'weight.*' => 'numeric|min:0',
        'designs.*' => 'required|array|min:1',
        'designs.*.*' => 'exists:fabric_measurements,id',
        'remarks' => 'nullable|string'
    ]);

    // Extract daily_production_item_ids and sequences from the lace_qty/weight keys
    $itemsData = [];
    foreach ($validated['lace_qty'] as $key => $value) {
        // Handle both existing item IDs and new item format (parentId-sequence)
        if (strpos($key, '-') !== false) {
            [$parentId, $sequence] = explode('-', $key);
        } else {
            $parentId = $key;
            $sequence = 1;
        }
        
        $itemsData[] = [
            'key' => $key,
            'parentId' => $parentId,
            'sequence' => $sequence,
            'lace_qty' => $value,
            'weight' => $validated['weight'][$key],
            'designs' => $validated['designs'][$key] ?? []
        ];
    }

    DB::transaction(function () use ($validated, $itemsData, $thanIssue) {
        
        // Update the main Than Issue
        $thanIssue->update([
            'issue_date' => $validated['date'],
            'product_group_id' => $validated['product_group_id'],
            'remarks' => $validated['remarks'] ?? null
        ]);

        // Get existing items for comparison
        $existingItems = $thanIssue->items()->with('fabricMeasurements')->get();
        $existingItemIds = $existingItems->pluck('id')->toArray();
        $processedItemIds = [];

        // Process each item
        foreach ($itemsData as $item) {
            // Check if this is an existing item (numeric key) or new item (contains hyphen)
            $isExistingItem = is_numeric($item['key']);
            
            if ($isExistingItem) {
                // Update existing item
                $thanIssueItem = $existingItems->where('id', $item['key'])->first();
                
                if ($thanIssueItem) {
                    $thanIssueItem->update([
                        'product_group_id' => $validated['product_group_id'],
                        'lace_qty' => $item['lace_qty'],
                        'weight' => $item['weight']
                    ]);

                    // Update fabric measurements
                    $thanIssueItem->fabricMeasurements()->sync($item['designs']);
                    $processedItemIds[] = $thanIssueItem->id;
                }
            } else {
                // Create new item
                $dailyItem = DailyProductionItem::with('saleOrderItem')->find($item['parentId']);
                
                if ($dailyItem) {
                    $thanIssueItem = ThanIssueItem::create([
                        'than_issue_id' => $thanIssue->id,
                        'daily_production_item_id' => $item['parentId'],
                        'product_group_id' => $validated['product_group_id'],
                        'quantity' => 1,
                        'lace_qty' => $item['lace_qty'],
                        'weight' => $item['weight']
                    ]);

                    // Attach fabric measurements to the new than issue item
                    if (!empty($item['designs'])) {
                        $thanIssueItem->fabricMeasurements()->attach($item['designs']);
                    }
                }
            }
        }

        // Delete items that were removed (not in the processed list)
        $itemsToDelete = array_diff($existingItemIds, $processedItemIds);
        
        if (!empty($itemsToDelete)) {
            // First detach fabric measurements relationships
            foreach ($itemsToDelete as $itemId) {
                $item = ThanIssueItem::find($itemId);
                if ($item) {
                    $item->fabricMeasurements()->detach();
                }
            }
            
            // Then delete the items
            ThanIssueItem::whereIn('id', $itemsToDelete)->delete();
        }
    });

    return redirect()->route('than-issues.index')
        ->with('success', 'Than Issue updated successfully.');
}

    public function destroy(ThanIssue $thanIssue)
    {
        $thanIssue->delete();
        return redirect()->route('than-issues.index')->with('success', 'Than Issue deleted successfully.');
    }

    public function getParties()
    {
        return Party::all();
    }
}
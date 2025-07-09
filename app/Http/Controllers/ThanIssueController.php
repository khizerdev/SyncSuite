<?php

namespace App\Http\Controllers;

use App\Models\ThanIssue;
use App\Models\ProductGroup;
use App\Models\Department;
use App\Models\Party;
use App\Models\DailyProduction;
use App\Models\DailyProductionItem;
use App\Models\ThanIssueItem;
use Illuminate\Http\Request;
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
        
        return view('pages.than-issues.create', compact(
            'dailyProductions',
            'productGroups',
            'departments',
            'parties'
        ));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'daily_production_item_ids' => 'required|array',
        'daily_production_item_ids.*' => 'exists:daily_production_items,id',
        'product_group_id' => 'required|exists:product_groups,id',
        'production_search' => 'required|string',
        'remarks' => 'nullable|string'
    ]);

    // Generate simple sequential serial for ThanIssue (th-001, th-002, etc.)
    $lastThanIssue = ThanIssue::orderBy('id', 'desc')->first();
    $thanSerialNumber = 'th-' . str_pad(($lastThanIssue ? $lastThanIssue->id + 1 : 1), 3, '0', STR_PAD_LEFT);

    // Create the main Than Issue
    $thanIssue = ThanIssue::create([
        'issue_date' => $validated['date'],
        'daily_production_id' => DailyProductionItem::find($validated['daily_production_item_ids'][0])->daily_production_id,
        'product_group_id' => $validated['product_group_id'],
        'remarks' => $validated['remarks'] ?? null,
        'serial_no' => $thanSerialNumber  // Add the simple serial number
    ]);

    // Get the last serial number with the same prefix to continue numbering for ThanIssueItem
    // Get the last serial number with the same prefix to continue numbering
    $lastSerial = ThanIssueItem::where('serial_no', 'like', $validated['production_search'] . '-%')
        ->orderBy('serial_no', 'desc')
        ->first();

    $serialNumber = 1;
    if ($lastSerial) {
        preg_match('/-(\d+)$/', $lastSerial->serial_no, $matches);
        $serialNumber = (int)$matches[1] + 1;
    }

    // Create a single Than Issue Item with all daily production item IDs
    foreach ($validated['daily_production_item_ids'] as $itemId) {
        $dailyItem = DailyProductionItem::find($itemId);
        
        ThanIssueItem::create([
            'than_issue_id' => $thanIssue->id,
            'daily_production_item_id' => $itemId, // Store single ID
            'product_group_id' => $validated['product_group_id'],
            'quantity' => $dailyItem->than_qty ?? 1, // Use item's quantity or default to 1
            'serial_no' => $validated['production_search'] . '-' . str_pad($serialNumber++, 3, '0', STR_PAD_LEFT)
        ]);
    }

    return redirect()->route('than-issues.index')
        ->with('success', 'Than Issue created successfully with serial numbers.');
}

    public function show(ThanIssue $thanIssue)
    {
        return view('pages.than-issues.show', compact('thanIssue'));
    }

    public function edit(ThanIssue $thanIssue)
    {
        $productGroups = ProductGroup::all();
        $departments = Department::all();
        $parties = Party::all();
        return view('pages.than-issues.edit', compact('thanIssue', 'productGroups', 'departments', 'parties'));
    }

    public function update(Request $request, ThanIssue $thanIssue)
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'product_group_id' => 'required|exists:product_groups,id',
            'job_type' => 'required|in:department,party',
            'department_id' => 'required_if:job_type,department|exists:departments,id',
            'party_id' => 'required_if:job_type,party|exists:parties,id',
        ]);

        $thanIssue->update($validated);

        return redirect()->route('than-issues.index')->with('success', 'Than Issue updated successfully.');
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
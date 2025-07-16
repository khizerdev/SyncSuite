<?php

namespace App\Http\Controllers;

use App\Models\ThanIssue;
use App\Models\ThanSupply;
use App\Models\ProductGroup;
use App\Models\Department;
use App\Models\Party;
use App\Models\DailyProduction;
use App\Models\DailyProductionItem;
use App\Models\ThanIssueItem;
use App\Models\ThanSupplyItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class ThanSupplyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
             $data = ThanSupply::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('than-supplies.edit', $row->id);
                    $deleteUrl = route('than-supplies.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        return view('pages.than-supplies.index');
    }

    public function create()
    {
     
        
        $departments = Department::all();
        $parties = Party::all();
        
        return view('pages.than-supplies.create', compact(
            'departments',
            'parties'
        ));
    }

    public function store(Request $request)
{
    DB::beginTransaction();
    
    try {
        // Generate the serial number for than_supplies
        $latestSupply = ThanSupply::latest()->first();
        $serialNumber = 'TS-0001'; // Default if no records exist
        
        if ($latestSupply) {
            // Extract the numeric part and increment
            $number = (int) substr($latestSupply->serial_no, 3);
            $serialNumber = 'TS-' . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
        }
        
        // Create the than_supply record
        $thanSupply = ThanSupply::create([
            'serial_no' => $serialNumber,
            'issue_date' => $request->date,
            // 'job_type' => $request->job_type,
            // 'department_id' => $request->department_id,
            // 'party_id' => $request->party_id,
            'remarks' => $request->remarks,
        ]);
        
        // Create than_supply_items records
        foreach ($request->items as $item) {
            ThanSupplyItem::create([
                'serial_no' => $item['serial_no'],
                'than_supply_id' => $thanSupply->id,
                'daily_issue_item_id' => $item['daily_production_item_id'],
                // 'product_group_id' => $item['product_group_id'],
                'quantity' => $item['quantity'],
            ]);
        }
        
        DB::commit();
        
        return redirect()->route('than-supplies.index')
            ->with('success', 'Than Supply created successfully.');
            
    } catch (\Exception $e) {
        dd($e);
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Error creating Than Supply: ' . $e->getMessage());
    }
}

    public function show(ThanIssue $thanIssue)
    {
        return view('pages.than-supplies.show', compact('thanIssue'));
    }

    public function edit(ThanIssue $thanIssue)
    {
        $productGroups = ProductGroup::all();
        $departments = Department::all();
        $parties = Party::all();
        return view('pages.than-supplies.edit', compact('thanIssue', 'productGroups', 'departments', 'parties'));
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

        return redirect()->route('than-supplies.index')->with('success', 'Than Issue updated successfully.');
    }

    public function destroy(ThanIssue $thanIssue)
    {
        $thanIssue->delete();
        return redirect()->route('than-supplies.index')->with('success', 'Than Issue deleted successfully.');
    }

    public function getParties()
    {
        return Party::all();
    }
}
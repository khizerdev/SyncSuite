<?php

namespace App\Http\Controllers;

use App\Models\DailyProduction;
use App\Models\Machine;
use App\Models\Shift;
use Illuminate\Http\Request;
use DataTables;

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
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'machine_id' => 'required|exists:machines,id',
            'current_stitch' => 'required|integer',
            'saleorder_id' => 'required'
        ]);

        DailyProduction::create([
            'shift_id' => $request->shift_id,
            'date' => $request->date,
            'machine_id' => $request->machine_id,
            'current_stitch' => $request->current_stitch,
            'description' => $request->description,
            'needles' => json_encode($request->needle),
            'sale_order_id' => $request->saleorder_id,
        ]);

        return redirect()->route('daily-productions.index')
                         ->with('success', 'Daily Production created successfully.');
    }


    public function edit(DailyProduction $dailyProduction)
    {
        $shifts = Shift::all();
        $machines = Machine::all();
        return view('pages.daily-productions.edit', compact('dailyProduction', 'shifts', 'machines'));
    }

    public function update(Request $request, DailyProduction $dailyProduction)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'machine_id' => 'required|exists:machines,id',
            'current_stitch' => 'required|integer',
        ]);

        $dailyProduction->update($request->all());

        return redirect()->route('daily-productions.index')
                         ->with('success', 'Daily Production updated successfully');
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
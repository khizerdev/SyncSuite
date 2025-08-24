<?php

namespace App\Http\Controllers;

use App\Models\ThanSupply;
use App\Models\Department;
use App\Models\SupplyReceipt;
use Illuminate\Http\Request;
use DataTables;

class SupplyReceiptController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SupplyReceipt::with(['thanSupply', 'department']);
            return DataTables::of($data)
            ->addColumn('action', function($row){
                // $editUrl = route('supply-receipts.edit', $row->id);
                $deleteUrl = route('supply-receipts.destroy', $row->id);

                // $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn = '';
                $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/supply-receipts/destroy/\', \'GET\')" class="delete btn btn-danger btn-sm">Delete</button>';
                return $btn;
                return '';
            })
            ->addColumn('serial_no', function($row) {
                return $row->thanSupply ? $row->thanSupply->serial_no : 'N/A';
            })
            ->editColumn('received_date', function($row) {
                return $row->received_date->format('d/m/Y');
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->editColumn('updated_at', function($row) {
                return $row->updated_at->format('d/m/Y H:i');
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.supply-receipts.index');
    }
    
    public function create()
    {
        $departments = Department::all();
        return view('pages.supply-receipts.create', compact('departments'));
    }

    public function getSupplies(Request $request)
    {
        $departmentId = $request->department_id;
        
        // Get supplies for this department that haven't been received yet
        $supplies = ThanSupply::where('department_id', $departmentId)
            ->whereDoesntHave('receipts', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->get();
            
        return response()->json($supplies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'supplies' => 'required|array',
            'supplies.*' => 'exists:than_supplies,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        foreach ($validated['supplies'] as $supplyId) {
            SupplyReceipt::create([
                'than_supply_id' => $supplyId,
                'department_id' => $validated['department_id'],
                'received_date' => $validated['received_date'],
                'notes' => $validated['notes']
            ]);
        }
        
        return redirect()->back()->with('success', 'Supplies received successfully!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $supply = SupplyReceipt::findOrFail($id);

            $supply->delete();

            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}
<?php 

namespace App\Http\Controllers;

use App\Models\FabricMeasurement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FabricMeasurementController extends Controller
{
    public function create()
    {
        return view('pages.fabric_measurements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_of_measure' => 'required|string|max:255',
            'design_stitch' => 'required|string|max:255',
            'front_yarn' => 'required|string|max:255',
            'back_yarn' => 'required|string|max:255',
        ]);

        FabricMeasurement::create($request->all());

        return redirect()->route('fabric-measurements.index')->with('success', 'Record created successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FabricMeasurement::latest()->get();
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $editUrl = route('fabric-measurements.edit', $row->id);
                
                $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                
                // $btn = $edit;
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.fabric_measurements.index');
    }

    public function edit($id)
    {
        $measurement = FabricMeasurement::findOrFail($id);
        return view('pages.fabric_measurements.edit', compact('measurement'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_of_measure' => 'required|string|max:255',
            'design_stitch' => 'required|string|max:255',
            'front_yarn' => 'required|string|max:255',
            'back_yarn' => 'required|string|max:255',
        ]);

        $measurement = FabricMeasurement::findOrFail($id);
        $measurement->update($request->all());

        return redirect()->route('fabric-measurements.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $module = FabricMeasurement::findOrFail($id);

            $module->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
        
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class RawMaterialController extends Controller
{
    
    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $data = RawMaterial::select('*');
            return DataTables::of($data)
            ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $editUrl = route('raw-materials.edit', $row->id);
                        
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit;
                        return $btn;
                    })
             
                ->rawColumns(['action'])
                ->make(true);
        }
    
        return view('pages.raw-materials.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.raw-materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:raw_materials',
            'product_name' => 'required',
            'complete_name' => 'required',
            'unit_of_measurement' => 'required',
            'type' => 'required',
            'rate' => 'required|numeric',
            'opening_qty' => 'required|integer',
        ]);

        RawMaterial::create($request->all());
        return redirect()->route('raw-materials.index')->with('success', 'Created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterial $rawMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RawMaterial $rawMaterial)
    {
        return view('pages.raw-materials.edit', compact('rawMaterial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'code' => 'required|unique:raw_materials,code,' . $rawMaterial->id,
            'product_name' => 'required',
            'complete_name' => 'required',
            'unit_of_measurement' => 'required',
            'type' => 'required',
            'rate' => 'required|numeric',
            'opening_qty' => 'required|integer',
        ]);

        $rawMaterial->update($request->all());
        return redirect()->route('raw-materials.index')->with('success', 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = RawMaterial::findOrFail($id);
           
            $item->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}

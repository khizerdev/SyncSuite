<?php

namespace App\Http\Controllers;

use App\Models\ColorCode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ColorCodeController extends Controller
{
    // Show form
    public function create()
    {
        return view('pages.color_codes.create');
    }

    // Store data
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);

        ColorCode::create($request->all());

        return redirect()->route('color-codes.index')->with('success', 'Color Code added!');
    }

    // Show all records (Yajra Datatable)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ColorCode::latest()->get();
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $editUrl = route('color-codes.edit', $row->id);
                
                $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                
                // $btn = $edit;
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.color_codes.index');
    }

    // Edit form
    public function edit($id)
    {
        $colorCode = ColorCode::findOrFail($id);
        return view('pages.color_codes.edit', compact('colorCode'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);

        $colorCode = ColorCode::findOrFail($id);
        $colorCode->update($request->all());

        return redirect()->route('color-codes.index')->with('success', 'Color Code updated!');
    }

    // Delete data
    public function destroy($id)
    {
        try {
            $module = ColorCode::findOrFail($id);

            $module->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}

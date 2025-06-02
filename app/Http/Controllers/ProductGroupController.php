<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\Http\Request;
use DataTables;

class ProductGroupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductGroup::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('product-groups.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button class="btn btn-danger btn-sm delete" data-id="'.$row->id.'">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('pages.product-groups.index');
    }

    public function create()
    {
        return view('pages.product-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'prefix' => 'required|string|max:255',
        ]);

        ProductGroup::create($request->all());

        return redirect()->route('product-groups.index')
                         ->with('success', 'Product Group created successfully.');
    }

    public function edit($id)
    {
        $productGroup = ProductGroup::find($id);
        return view('pages.product-groups.edit', compact('productGroup'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'prefix' => 'required|string|max:255',
        ]);

        $productGroup = ProductGroup::find($id);
        $productGroup->update($request->all());

        return redirect()->route('product-groups.index')
                         ->with('success', 'Product Group updated successfully');
    }

    public function destroy($id)
    {
        ProductGroup::find($id)->delete();
        return response()->json(['success' => 'Product Group deleted successfully.']);
    }
}
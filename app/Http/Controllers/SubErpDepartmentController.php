<?php

namespace App\Http\Controllers;

use App\Models\SubErpDepartment;
use App\Models\ErpDepartment;
use Illuminate\Http\Request;

class SubErpDepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
             $data = SubErpDepartment::with('department')->latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('sub-erp-departments.edit', $row->id);
                    $deleteUrl = route('sub-erp-departments.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        
        return view('pages.sub-erp-departments.index');
    }

    public function create()
    {
        $departments = ErpDepartment::all();
        return view('pages.sub-erp-departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:erp_departments,id',
        ]);

        SubErpDepartment::create($request->all());

        return redirect()->route('sub-erp-departments.index')
            ->with('success', 'Sub Department created successfully.');
    }


    public function edit(SubErpDepartment $subErpDepartment)
    {
        $departments = ErpDepartment::all();
        return view('pages.sub-erp-departments.edit', compact('subErpDepartment', 'departments'));
    }

    public function update(Request $request, SubErpDepartment $subErpDepartment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:erp_departments,id',
        ]);

        $subErpDepartment->update($request->all());

        return redirect()->route('sub-erp-departments.index')
            ->with('success', 'Sub Department updated successfully');
    }

    public function destroy(SubErpDepartment $subErpDepartment)
    {
        try {
          
            $erpDepartment->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete branch', 'error' => $e->getMessage()], 500);
        }
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\ErpDepartment;
use Illuminate\Http\Request;
use DataTables;

class ErpDepartmentController extends Controller
{
    public function index(Request $request)
     {
        if ($request->ajax()) {
             $data = ErpDepartment::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('erp-departments.edit', $row->id);
                    $deleteUrl = route('erp-departments.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        
        return view('pages.erp-departments.index');
     }

    public function create()
    {
        return view('pages.erp-departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:erp_departments,title',
        ]);

        ErpDepartment::create($request->all());

        return redirect()->route('erp-departments.index')
            ->with('success', 'Department created successfully.');
    }


    public function edit(ErpDepartment $erpDepartment)
    {
        return view('pages.erp-departments.edit', compact('erpDepartment'));
    }

    public function update(Request $request, ErpDepartment $erpDepartment)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:erp_departments,title,'.$erpDepartment->id,
        ]);

        $erpDepartment->update($request->all());

        return redirect()->route('erp-departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(ErpDepartment $erpDepartment)
    {
        try {
          
            

            $erpDepartment->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete branch', 'error' => $e->getMessage()], 500);
        }
    }

    public function getDepartments(Request $request)
    {
        $data = ErpDepartment::select('id', 'title', 'created_at');

        return datatables()->of($data)
            ->addColumn('action', function($row){
                $btn = '<a href="'.route('erp-departments.edit', $row->id).'" class="edit btn btn-primary btn-sm">Edit</a>';
                $btn .= ' <form action="'.route('erp-departments.destroy', $row->id).'" method="POST" style="display:inline;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>
                          </form>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
     // app/Http/Controllers/DepartmentController.php
    public function getSubDepartments(ErpDepartment $department)
    {
        return response()->json($department->subDepartments);
    }
}
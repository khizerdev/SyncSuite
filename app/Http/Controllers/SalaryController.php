<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Salary;
use App\Models\Shift;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Salary::all();
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $editUrl = route('shifts.edit', $row->id);
                $deleteUrl = route('shifts.destroy', $row->id);

                $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn .= '<button onclick="deleteData(\'' . $row->id . '\', \'/shifts/\', \'DELETE\')" class="delete btn btn-danger btn-sm">Delete</button>';
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.salary.index');
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('pages.shifts.edit',compact('shift'));
    }

    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);
            $shift->delete();
    
            return response()->json(['message' => 'Shift deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
}
<?php

namespace App\Http\Controllers;

use App\Models\ThanIssue;
use App\Models\ProductGroup;
use App\Models\Department;
use App\Models\Party;
use Illuminate\Http\Request;
use DataTables;

class ThanIssueController extends Controller
{
    public function index(Request $request)
    {
        
        if ($request->ajax()) {
             $data = ThanIssue::latest()->get();
             return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = route('than-issues.edit', $row->id);
                    $deleteUrl = route('than-issues.destroy', $row->id);

                    $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        
        return view('pages.than-issues.index');
    }

    public function create()
    {
        $productGroups = ProductGroup::all();
        $departments = Department::all();
        $parties = Party::all();
        return view('pages.than-issues.create', compact('productGroups', 'departments', 'parties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'product_group_id' => 'required|exists:product_groups,id',
            'job_type' => 'required|in:department,party',
            'department_id' => 'required_if:job_type,department|exists:departments,id',
            'party_id' => 'required_if:job_type,party|exists:parties,id',
        ]);

        ThanIssue::create($validated);

        return redirect()->route('than-issues.index')->with('success', 'Than Issue created successfully.');
    }

    public function show(ThanIssue $thanIssue)
    {
        return view('than-issues.show', compact('thanIssue'));
    }

    public function edit(ThanIssue $thanIssue)
    {
        $productGroups = ProductGroup::all();
        $departments = Department::all();
        $parties = Party::all();
        return view('than-issues.edit', compact('thanIssue', 'productGroups', 'departments', 'parties'));
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

        return redirect()->route('than-issues.index')->with('success', 'Than Issue updated successfully.');
    }

    public function destroy(ThanIssue $thanIssue)
    {
        $thanIssue->delete();
        return redirect()->route('than-issues.index')->with('success', 'Than Issue deleted successfully.');
    }

    public function getParties()
    {
        return Party::all();
    }
}
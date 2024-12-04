<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\Shift;
use App\Services\SalaryService;
use Carbon\Carbon;
use Exception;
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

    public function generateSalary()
    {
        $departments = Department::all();
        return view('pages.salary.generate-salary', compact('departments'));
    }

    public function processSalaryGeneration(Request $request)
    {
        $currentMonth = intval($request->month);
        $currentYear = now()->year;
        $period = $request->period;

        $timestamp = mktime(0, 0, 0, $currentMonth, 1, 1970);

        $month_name = date("F", $timestamp);

        //  check start and end dates
        if ($period === 'first_half') {
            $startDate = Carbon::parse($month_name)->startOfMonth();
            $endDate = Carbon::parse($month_name)->startOfMonth()->addDays(14);
        } elseif ($period === 'second_half') {
            $startDate = Carbon::parse($month_name)->startOfMonth()->addDays(15);
            $endDate = Carbon::parse($month_name)->endOfMonth();
        } else {
            // full_month
            $startDate = Carbon::parse($month_name)->startOfMonth();
            $endDate = Carbon::parse($month_name)->endOfMonth();
        }

        $unresolvedExceptions = Employee::with(['loans'])
            ->whereHas('loans', function ($query) {
                $query->whereColumn('paid', '<', 'amount');
            })
            ->whereDoesntHave('loanExceptions', function ($query) use ($currentMonth, $currentYear) {
                $query->where('month', $currentMonth)
                    ->where('year', $currentYear);
            })
            ->get();

        if ($unresolvedExceptions->isNotEmpty()) {
            return redirect()->back()->with('error', 'Salary generation is blocked. There are unresolved loan excemptions for some employees that need to be clarified.');
        }

        $departmentId = $request->input('department_id');
        $employees = Employee::where('department_id', $departmentId)
        ->when($period == "first_half" || $period == "second_half", function($query) {
            return $query->where('salary_duration', 'half_month');
        })
        ->when($period == "full_month", function($query) {
            return $query->where('salary_duration', 'full_month');
        })
        ->get();

        foreach ($employees as $employee) {
            try {
                $salaryService = new SalaryService();
                $salaryService->calculateSalary($employee->id, $startDate, $endDate, $period);
            } catch (Exception $e){
                dd($e);
            }
        }

        return redirect()->route('generate-salary')->with('success', 'Salaries generated successfully.');
    }
    
}
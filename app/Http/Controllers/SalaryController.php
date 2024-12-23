<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\AdvanceSalary;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Salary;
use App\Models\Shift;
use App\Services\AttendanceService;
use App\Services\SalaryService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Salary::all();
            return DataTables::of($data)
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name;
            })
            ->addColumn('action', function($row){
                // $editUrl = route('shifts.edit', $row->id);
                // $deleteUrl = route('shifts.destroy', $row->id);

                // $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                $btn = '<button onclick="deleteData(\'' . $row->id . '\', \'/salaries/\', \'DELETE\')" class="delete btn btn-danger btn-sm">Delete</button>';
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
            $salary = Salary::findOrFail($id);
            $salary->delete();
    
            return response()->json(['message' => 'Salary deleted successfully'], 200);
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
        $departmentId = $request->input('department_id');

        $unresolvedExceptions = Employee::with(['loans'])
        ->where('department_id', $departmentId)
        ->whereHas('loans', function ($query) {
            $query->whereColumn('paid', '<', 'amount');
        })
        ->whereNotExists(function ($query) use ($currentMonth, $currentYear, $period) {
            $query->select(DB::raw(1))
                ->from('loan_exceptions')
                ->whereColumn('loan_exceptions.employee_id', 'employees.id')
                ->where('loan_exceptions.month', $currentMonth)
                ->where('loan_exceptions.year', $currentYear)
                ->where('loan_exceptions.salary_duration', $period);
        })
        ->get();

        if ($unresolvedExceptions->isNotEmpty()) {
            return redirect()->back()->with(
                'error',
                'Salary generation is blocked. There are unresolved loan exceptions for some employees in the selected period.'
            );
        }

        $employees = Employee::where('department_id', $departmentId)
        ->when($period == "first_half" || $period == "second_half", function($query) {
            return $query->where('salary_duration', 'half_month');
        })
        ->when($period == "full_month", function($query) {
            return $query->where('salary_duration', 'full_month');
        })
        ->get();

        foreach ($employees as $employee) {
            $salary = Salary::where('employee_id' , $employee->id)->where('month', $request->month)
            ->where('year', $currentYear)->where('period' , $period)->first();
            if(!$salary) {
                try {
                    
                    $processor = new AttendanceService($employee);
                    $result = $processor->processAttendance($startDate, $endDate);
                    if (empty(array_filter($result['groupedAttendances'], function ($value) {
                        return !empty($value);
                    }))) {
                    } else {
                        $salaryService = new SalaryService($employee, $result);
                        $salary = $salaryService->calculateSalary($employee->id, $startDate, $endDate, $period, $currentMonth);
                        
                        $salaryData = array_merge($result,$salary);
                        // dd($salaryData);
        
                        $advance = AdvanceSalary::where('employee_id', $employee->id)->latest()->first();
        
                        $loan = Loan::where('employee_id', $employee->id)->whereColumn('paid', '<', 'amount')->first();
                        $loanInstallmentAmount = isset($loan) ? $loan->amount / $loan->months : 0;
        
                        $loanException = $employee->loanExceptions()->where('month', $month_name)
                        ->where('year', $currentYear)
                        ->first();
        
                        DB::transaction(function () use ($loan, $loanException, $employee, $salaryData, $advance, $loanInstallmentAmount, $currentMonth, $currentYear, $period, $startDate, $endDate) {
                            if($loan && $loanException && !$loanException->is_approved){
                                $loan->paid += $loan->amount / $loan->months;
                                $loan->save();
                            }
                            
                            $data = [
                                'employee_id' => $employee->id,
                                'month' => $currentMonth,
                                'year' => $currentYear,
                                'current_salary' => $employee->salary,
                                'expected_hours' => $salaryData['totalExpectedWorkingHours'],
                                'normal_hours' => $salaryData['totalHoursWorked'],
                                'holiday_hours' => $salaryData['totalHolidayHoursWorked'],
                                'overtime_hours' => $salaryData['totalOvertimeMinutes']/60,
                                'salary_per_hour' => $employee->salary/$salaryData['totalExpectedWorkingHours'],
                                'holiday_pay_ratio' => $employee->type->holiday_ratio,
                                'overtime_pay_ratio' => $employee->type->overtime_ratio,
                                'overtime_hours' => $salaryData['totalOverTimeHoursWorked'],
                                'holidays' => $employee->type->holidays,
                                'advance_deducted' => $advance ? $advance->amount : 0,
                                'period' => $period,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'loan_deducted' => $loanException && $loanException->is_approved ? 0 : $loanInstallmentAmount,
                            ];
                            
                            Salary::create($data);
                            
                            if ($advance) {
                                $advance->is_paid = 1;
                                $advance->save();
                            }
                        });
                    }
    
                } catch (Exception $e){
                    throw $e;
                }
            }
        }

        return redirect()->route('generate-salary')->with('success', 'Salaries generated successfully.');
    }
    
}
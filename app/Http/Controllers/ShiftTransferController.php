<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftTransfer;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;

class ShiftTransferController extends Controller
{
    public function index()
    {
        $shiftTransfers = ShiftTransfer::with(['employee.department', 'shift'])->get();
        $departments = Department::all();
        $shifts = Shift::all();
        
        return view('pages.shift-transfers.index', compact('shiftTransfers', 'departments', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'from_date' => 'required|date',
        ]);

        $employeeIds = $request->input('employee_ids');
        $shiftId = $request->input('shift_id');
        $fromDate = $request->input('from_date');

        // Check for existing shift transfers to prevent duplicates
        $existingTransfers = ShiftTransfer::whereIn('employee_id', $employeeIds)
            ->where('from_date', '>=', $fromDate)
            ->get();

        if ($existingTransfers->count() > 0) {
            $existingEmployeeIds = $existingTransfers->pluck('employee_id')->toArray();
            $existingEmployeeNames = Employee::whereIn('id', $existingEmployeeIds)->pluck('name')->toArray();
            
            return redirect()->route('shift-transfers.index')
                ->with('warning', 'Some employees already have future shift transfers: ' . implode(', ', $existingEmployeeNames));
        }

        try {
            foreach ($employeeIds as $employeeId) {
                ShiftTransfer::create([
                    'employee_id' => $employeeId,
                    'shift_id' => $shiftId,
                    'from_date' => $fromDate,
                ]);
            }

            return redirect()->route('shift-transfers.index')
                ->with('success', 'Shift transfers created successfully for ' . count($employeeIds) . ' employee(s).');
        } catch (\Exception $e) {
            return redirect()->route('shift-transfers.index')
                ->with('error', 'Error creating shift transfers: ' . $e->getMessage());
        }
    }

    public function destroy(ShiftTransfer $shiftTransfer)
    {
        try {
            $shiftTransfer->delete();
            return redirect()->route('shift-transfers.index')
                ->with('success', 'Shift transfer deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('shift-transfers.index')
                ->with('error', 'Error deleting shift transfer');
        }
    }

    /**
     * Get employees by department (API endpoint)
     */
    public function getEmployeesByDepartment($departmentId)
    {
        try {
            $employees = Employee::with(['department', 'currentShift'])
                ->where('department_id', $departmentId)
                ->select('id', 'name', 'department_id','code')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'code' => $employee->code,
                        'department' => $employee->department ? [
                            'id' => $employee->department->id,
                            'name' => $employee->department->name
                        ] : null,
                        'current_shift' => $employee->currentShift ? [
                            'id' => $employee->currentShift->id,
                            'name' => $employee->currentShift->name,
                            'start_time' => $employee->currentShift->start_time,
                            'end_time' => $employee->currentShift->end_time
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shift transfer data for DataTables (if needed)
     */
    public function getShiftTransfersData(Request $request)
    {
        if ($request->ajax()) {
            $data = ShiftTransfer::with(['employee.department', 'shift'])
                ->select('shift_transfers.*');

            return DataTables::of($data)
                ->addColumn('employee_name', function ($row) {
                    return $row->employee->name ?? 'N/A';
                })
                ->addColumn('employee_id', function ($row) {
                    return $row->employee->employee_id ?? 'N/A';
                })
                ->addColumn('department_name', function ($row) {
                    return $row->employee->department->name ?? 'N/A';
                })
                ->addColumn('shift_name', function ($row) {
                    return $row->shift->name . ' (' . $row->shift->start_time . ' - ' . $row->shift->end_time . ')';
                })
                ->addColumn('formatted_date', function ($row) {
                    return \Carbon\Carbon::parse($row->from_date)->format('d M Y');
                })
                ->addColumn('created_date', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '<form action="' . route('shift-transfers.destroy', $row->id) . '" method="POST" class="delete-form d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
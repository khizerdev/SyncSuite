<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftTransfer;
use Yajra\DataTables\Facades\DataTables;

class ShiftTransferController extends Controller
{
    public function index()
    {
        $shiftTransfers = ShiftTransfer::with(['employee', 'shift'])->get();
        $employees = Employee::all();
        $shifts = Shift::all();

        return view('pages.shift-transfers.index', compact('shiftTransfers', 'employees', 'shifts'));
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

    foreach ($employeeIds as $employeeId) {
        ShiftTransfer::create([
            'employee_id' => $employeeId,
            'shift_id' => $shiftId,
            'from_date' => $fromDate,
        ]);
    }

    return redirect()->route('shift-transfers.index')->with('success', 'Shift transfers created successfully.');
}
    
}
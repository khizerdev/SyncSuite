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
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'from_date' => 'required|date',
        ]);

        ShiftTransfer::create($request->only(['employee_id', 'shift_id', 'from_date']));

        return redirect()->route('shift-transfers.index')->with('success', 'Created successfully.');
    }
    
}
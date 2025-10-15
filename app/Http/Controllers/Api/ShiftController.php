<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'start_time' => 'required|date_format:H:i',
    'end_time' => 'required|date_format:H:i|after:start_time',
    'overtime_limit' => 'required|numeric|min:0',
]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $shift = Shift::create([
    'name' => $request->name,
    'start_time' => $request->start_time,
    'end_time' => $request->end_time,
    'overtime_limit' => $request->overtime_limit,
]);
        
        return response()->json([
            'message' => 'Shift created successfully', 
            'shift' => $shift
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred', 
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id){
        try {
            $shift = Shift::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $shift->update($request->all());

            return response()->json(['message' => 'Shift updated successfully', 'shift' => $shift], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the shift', 'error' => $e->getMessage()], 500);
        }
    }
    
}
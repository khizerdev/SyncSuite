<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InwardGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InwardGeneralController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_number' => 'required|string|max:255',
            'party' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.particular' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.remarks' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $validatedData = $validator->validated();
            $inwardData = [
                'ref_number' => $validatedData['ref_number'],
                'party' => $validatedData['party'],
                'department' => $validatedData['department'],
                'date' => $validatedData['date'],
                'description' => $validatedData['description'],
            ];
            $inwardGeneral = InwardGeneral::create($inwardData);

            foreach ($request->items as $item) {
                $itemData = [
                    'particular' => $item['particular'],
                    'qty' => $item['qty'],
                    'remarks' => $item['remarks'],
                ];
                $inwardGeneral->items()->create($itemData);
            }

            DB::commit();

            return response()->json($inwardGeneral->load('items'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ref_number' => 'required|string|max:255',
            'party' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.particular' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.remarks' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $validatedData = $validator->validated();
            $inwardData = [
                'ref_number' => $validatedData['ref_number'],
                'party' => $validatedData['party'],
                'department' => $validatedData['department'],
                'date' => $validatedData['date'],
                'description' => $validatedData['description'],
            ];
            $inwardGeneral = InwardGeneral::findOrFail($id);
            $inwardGeneral->update($inwardData);
            $inwardGeneral->items()->delete();

            foreach ($request->items as $item) {
                $itemData = [
                    'particular' => $item['particular'],
                    'qty' => $item['qty'],
                    'remarks' => $item['remarks'],
                ];
                $inwardGeneral->items()->create($itemData);
            }

            DB::commit();

            return response()->json($inwardGeneral->load('items'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }
}
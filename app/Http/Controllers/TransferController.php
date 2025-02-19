<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        
        if($request->ajax()){
            
            $data = Transaction::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $editUrl = route('accounts-transfers.edit', $row->id);
                        
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit;
                        return $btn;
                    
                    })

                    ->addColumn('sender', function($row){
                        $btn = $row->sender->account_name;
                        return $btn;
                    })
                    ->addColumn('receiver', function($row){
                        $btn = $row->receiver->account_name;
                        return $btn;
                    })
                    ->addColumn('amount', function($row){
                        $btn = $row->amount;
                        return $btn;
                    })
                    ->addColumn('date', function($row){
                        $btn = \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                        return $btn;
                    })
                    ->rawColumns(['action','date','from','to'])
                    ->make(true);
        }
    
        return view('pages.accounts-transfers.index');

    }

    public function create()
    {
        $accounts = Account::all();
        return view('pages.accounts-transfers.create',compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ]);
        
        Transaction::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'type' => 'Transfer',
            'remarks' => $request->remarks,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);
        
        return redirect()->route('accounts-transfers.index')->with('success','Created Successfully');
    }

    public function edit($id)
    {
        $accounts = Account::all();
        $module = Transaction::find($id);
        
        return view('pages.accounts-transfers.edit',compact('module','accounts'));
    }

    public function update(Request $request,$id)
    {
        $module = Transaction::Find($id);
        $request->validate([
            'amount' => 'required',
        ]);
        
        $module->receiver_id = $request->receiver_id;
        $module->sender_id = $request->sender_id;
        $module->type = 'Transfer';
        $module->remarks = $request->remarks;
        $module->amount = $request->amount;
        $module->date = $request->date;
        $module->save();
        
        return redirect()->route('accounts-transfers.index')->with('success','Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = Transaction::findOrFail($id);
           
            $item->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
}
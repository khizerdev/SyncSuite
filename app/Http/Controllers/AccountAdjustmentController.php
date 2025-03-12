<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\Datatables\Datatables;
use Auth;


class AccountAdjustmentController extends Controller
{
    public function index(Request $request){


            if($request->ajax()){
        
                $data = AccountAdjustment::with('account')->select('*');
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('account', function($row){
                        
                            $btn = $row->account->account_name;
                            return $btn;
                        })
                        ->addColumn('date', function($row){
                            $btn = \Carbon\Carbon::parse($row->date)->format('M-d-Y');
                            return $btn;
                        })
                        ->addColumn('action', function($row){
                            $editUrl = route('account-adjustments.edit', $row->id);
                            
                            $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                            $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                            
                            // $btn = $edit;
                            return $btn;
                        })

                        ->rawColumns(['action','account'])
                        ->make(true);
                        
            }

         return view('pages.account-adjustments.index');
       
    }

    public function create(){
        $accounts = Account::all();
        return view('pages.account-adjustments.create',compact('accounts'));
    }

    public function store(Request $request){

        $account = AccountAdjustment::create([
            "account_id" => $request->account_id,
            "date" => $request->date,
            "type" => $request->type,
            "rate" => $request->rate,
            "description" => $request->description,

        ]);

        return redirect()->route('account-adjustments.index')->with('success','Created Successfully');
    }

    public function edit($id)
    {
        $accounts = Account::all();
        $module = AccountAdjustment::where('id',$id)->first();

        return view('pages.account-adjustments.edit',compact('accounts','module'));
    }

    public function update(Request $request ,$id){

        $module = AccountAdjustment::Find($id);

        $module->account_id = $request->account_id;
        $module->date = $request->date;
        $module->type = $request->type;
        $module->rate = $request->rate;
        $module->description = $request->description;

        $module->update();

        return view('pages.account-adjustments.index')->with('success','Updated Successfully');
    }

    public function destroy($id)
    {
        try {
            $module = AccountAdjustment::findOrFail($id);

            $module->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}
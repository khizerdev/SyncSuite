<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Support\Facades\DB;
use Auth;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
        
            $data = Account::select('*');
            return FacadesDataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('balance', function($row){
                        $account = Account::findOrFail($row->id);
                        if($account){
                            $processor = new AccountService($account);
                            $btn = number_format($processor->account_balance(),2);
                        } else {
                            $btn = 0;
                        }
                        return $btn;
                    })
                    ->addColumn('action', function($row){
                        $editUrl = route('accounts.edit', $row->id);
                        
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit;
                        return $btn;
                    
                    })
                    ->rawColumns(['action','balance'])
                    ->make(true);
                    
        }

        return view('pages.accounts.index');
    }

    public function create()
    {
        return view('pages.accounts.create');
    }

    public function store(Request $request)
    {
        try {
            Account::create([
                "date" => $request->date,
                "account_type" => $request->account_type,
                "account_name" => $request->account_name,
                "account_title" => $request->account_title,
                "person_name" => $request->person_name,
                "account_number" => $request->account_number,
                "branch_code" => $request->branch_code,
                "balance_type" => $request->balance_type,
                "opening_balance" => $request->opening_balance,
            ]);

            return redirect()->route('accounts.index')->with('success', 'Created Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $module = Account::find($id); 
        return view('pages.accounts.edit',compact('module'));
    }

    public function update(Request $request,$id)
    {
          $module = Account::findOrFail($id);
          $module->date = $request->date;
          $module->account_type = $request->account_type;
          $module->account_name = $request->account_name;
          $module->account_title =  $request->account_title;
          $module->person_name = $request->person_name;
          $module->account_number = $request->account_number;
          $module->branch_code = $request->branch_code;
          $module->balance_type = $request->balance_type;
          $module->opening_balance = $request->opening_balance;
          $module->save();
          return view('pages.accounts.index')->with('success','Updated');
    }

    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);

            $account->delete();
    
            return response()->json(['message' => 'Account deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete account', 'error' => $e->getMessage()], 500);
        }
    }
    
}
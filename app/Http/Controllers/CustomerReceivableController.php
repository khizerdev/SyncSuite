<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Auth;
use App\ExpenseAccount;
use App\Transation;
use Yajra\Datatables\Datatables;
use App\Models\CustomerReceivable;
use App\Services\CustomerReceivableService;


class CustomerReceivableController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        if($request->ajax()){
        
            $data = CustomerReceivable::with('customer');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $editUrl = route('accounts-customersreceivables.edit', $row->id);
                        
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit;
                        return $btn;
                    })
                    ->addColumn('customer', function($row){
                        $btn = $row->customer ? $row->customer->name : '-';
                        return $btn;
                    })
                     ->addColumn('date', function($row){
                        $btn = \Carbon\Carbon::parse($row->date)->format('M-d-Y');
                        return $btn;
                    })
                    ->addColumn('receiver', function($row){
                        if($row->receive_in == 'Vendor'){
                            $btn = 'Vendor';       
                        }else{
                            $btn = 'Account';
                        }
                     
                        return $btn;
                    })
                    ->filterColumn('customer', function($query, $keyword) {
                $query->whereHas('customer', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
                    ->rawColumns(['action','balance'])
                    ->make(true);
                    
        }
    
        return view('pages.accounts-customersreceivables.index');


        
    }
    
   


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('pages.accounts-customersreceivables.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $id;
        if($request->receive_in == 'Vendor'){
            $id = $request->vendor_id ;
        }else{
            $id = $request->account_id ;
        }
        
        $account = CustomerReceivable::create([
              "date" => $request->date,
              "customer_id" => $request->customer_id,
              "receiver_id" => $id,
              "receive_in" => $request->receive_in,
              "payment_type" => $request->payment_type,
              "bank_name" => $request->bank_name,
              "branch_name" => $request->branch_name,
              "account_title" => $request->account_title,
              "cheque" => $request->cheque,
              "cheque_date" => $request->cheque_date,
              "amount" => $request->amount,
              "remarks" => $request->remarks,
        ]);
        
        return redirect()->route('accounts-customersreceivables.index')->with('success','Created Successfully');
    }


     /**
     * Show the form for editing the specified resource
     */
    public function edit($id)
    {
        $module = CustomerReceivable::find($id); 
        return view('pages.accounts-customersreceivables.edit',compact('module'));
    }
    
    public function getBalance(Request $request)
    {
       
        $customerId = $request->input('customer_id');
        $processor = new CustomerReceivableService();
        $result = $processor->customer_balance($customerId);
        // $balance = Con::customer_balance($customerId);
        
        return response()->json(['balance' => $result]);
    }


     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        
        $receiver_id;
        if($request->receive_in == 'Vendor'){
            $receiver_id = $request->vendor_id ;
        }else{
            $receiver_id = $request->account_id ;
        }
    
          $module = CustomerReceivable::Find($id);
        
          $module->date = $request->date;
          $module->customer_id = $request->customer_id;
          $module->receiver_id = $receiver_id;
          $module->receive_in = $request->receive_in;
          $module->payment_type = $request->payment_type;
          $module->bank_name = $request->bank_name;
          $module->branch_name = $request->branch_name;
          $module->account_title = $request->account_title;
          $module->cheque = $request->cheque;
          $module->cheque_date = $request->cheque_date;
          $module->amount = $request->amount;
          $module->remarks = $request->remarks;
          $module->save();
          
          return back()->with('success','Updated');
    }


    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $module = CustomerReceivable::findOrFail($id);

            $module->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
    
}
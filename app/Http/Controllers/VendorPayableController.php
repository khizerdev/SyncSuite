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
use App\Models\Vendor;
use App\Models\VendorPayable;
use Illuminate\Support\Facades\DB;

class VendorPayableController extends Controller
{
    public function index(Request $request)
    {

        if($request->ajax()){
        
            $data = VendorPayable::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($row){
                        $editUrl = route('accounts-vendors-payables.edit', $row->id);

                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>';

                        
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit.$delete;
                        return $btn;
                    
                    })
                    ->addColumn('vendor', function($row){
                        $btn = $row->vendor->name ?? $row->id;
                        return $btn;
                    })
                     ->addColumn('date', function($row){
                        $btn = \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                        return $btn;
                    })
                    ->addColumn('account', function($row){
                        $btn = $row->account->account_name;
                        return $btn;
                    })
                    ->addColumn('amount', function ($row) {
                        return number_format($row->amount, 2);
                    })
                    ->rawColumns(['action','balance'])
                    ->make(true);
                    
        }
    
        return view('pages.accounts-vendorspayables.index');
    }

    public function create()
    {
        return view('pages.accounts-vendorspayables.create');
    }

    public function getBalance($id)
    {
        $balance = 0; 
        $vendor = Vendor::find($id);
        
         if($vendor->balance_type == 'Credit'){
            $balance += $vendor->opening_balance;
        }else{
            $balance -= $vendor->opening_balance ;
        }

        // $vendor_adjustments = VendorAdjustment::where('vendor_id',$id)->get();
        // foreach ($vendor_adjustments as $item) {
        //     if($item->type == 'Credit'){
        //       $balance += $item->rate;
        //     }else{
        //        $balance -= $item->rate;   
        //     }
        // }
        
        $payable = VendorPayable::where('vendor_id',$id)->sum('amount');
        if($payable) {
            
            $balance += $payable;
        }
        
        // $customer = CustomerReceivable::where('receive_in','Vendor')->where('receiver_id',$id)->sum('amount');
        // $balance += $customer;
        
        // $purchaseInvoices = PurchaseInvoice::all();
        
        
        // foreach($purchaseInvoices as $key => $purchaseInvoice){
            
                
        //      $purchaseInvoicesSingle = $this->get_purchase_invoice($purchaseInvoice->id);
            
           
        //     if($purchaseInvoicesSingle['vendor_id'] == $id){
                
        //       $balance -= $purchaseInvoicesSingle['total'];
        //     }
        // }
        
        return response()->json(['balance' => $balance]);
    }

    public function store(Request $request)
    {
        VendorPayable::create([
              "date" => $request->date,
              "vendor_id" => $request->vendor_id,
              "account_id" => $request->account_id,
              "payment_type" => $request->payment_type,
              "bank_name" => $request->bank_name,
              "branch_name" => $request->branch_name,
              "account_title" => $request->account_title,
              "cheque" => $request->cheque,
              "cheque_date" => $request->cheque_date,
              "amount" => $request->amount,
        ]);
        
        return redirect()->route('accounts-vendors-payables.index')->with('success','Created Successfully');
    }

    public function edit($id)
    {
        $module = VendorPayable::find($id); 
        return view('pages.accounts-vendorspayables.edit',compact('module'));
    }
    
    public function destroy($id)
    {
        try {
            $item = VendorPayable::findOrFail($id);
           
            $item->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
    
}
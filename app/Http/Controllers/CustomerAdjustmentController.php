<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAdjustment;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;

class CustomerAdjustmentController extends Controller
{
    public function index(Request $request){


            if($request->ajax()){
        
                $data = CustomerAdjustment::with('customer')->select('*');
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('customer', function($row){
                        
                            $btn = $row->customer->name;
                            return $btn;
                        })
                        ->addColumn('date', function($row){
                            $btn = \Carbon\Carbon::parse($row->date)->format('M-d-Y');
                            return $btn;
                        })
                        ->addColumn('action', function($row){
                            $editUrl = route('customer-adjustments.edit', $row->id);
                            
                            $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                            $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                            
                            // $btn = $edit;
                            return $btn;
                        })

                        ->rawColumns(['action','customer'])
                        ->make(true);
                        
            }

         return view('pages.customer-adjustments.index');
        
    }

    public function create(){
        $customers = Customer::all();
        return view('pages.customer-adjustments.create',compact('customers'));
    }

    public function store(Request $request){

        $customer = CustomerAdjustment::create([
            "customer_id" => $request->customer_id,
            "date" => $request->date,
            "type" => $request->type,
            "rate" => $request->rate,
            "description" => $request->description,
        ]);

        return redirect()->route('customer-adjustments.index')->with('success','Created Successfully');
    }

    public function edit($id)
    {
        $customers = Customer::all();
        $module = CustomerAdjustment::where('id',$id)->first();

        return view('pages.customer-adjustments.edit',compact('customers','module'));
    }

    public function update(Request $request ,$id)
    {
        $module = CustomerAdjustment::Find($id);

        $module->customer_id = $request->customer_id;
        $module->date = $request->date;
        $module->type = $request->type;
        $module->rate = $request->rate;
        $module->description = $request->description;
        $module->update();

        return view('pages.customer-adjustments.index')->with('success','Updated Successfully');
    }

    public function destroy($id)
    {
        
        try {
            $module = CustomerAdjustment::findOrFail($id);

            $module->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}
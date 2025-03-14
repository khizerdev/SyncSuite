<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\Datatables\Datatables;
use Auth;

class VendorAdjustmentController extends Controller
{
    public function index(Request $request){


            if($request->ajax()){
        
                $data = VendorAdjustment::with('vendor')->select('*');
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('vendor', function($row){
                        
                            $btn = $row->vendor ? $row->vendor->name : '-';
                            return $btn;
                        })
                        ->addColumn('date', function($row){
                            $btn = \Carbon\Carbon::parse($row->date)->format('M-d-Y');
                            return $btn;
                        })
                        ->addColumn('action', function($row){
                            $editUrl = route('vendor-adjustments.edit', $row->id);
    
                            $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm mr-2"><i class="fas fa-edit" aria-hidden="true"></i></a>';
    
                            
                            $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                            
                            // $btn = $edit.$delete;
                            return $btn;
                        
                        })

                        ->rawColumns(['action','vendor'])
                        ->make(true);
                        
            }

         return view('pages.vendor-adjustments.index');
       
    }

    public function create(){
        $vendors = Vendor::all();
        return view('pages.vendor-adjustments.create',compact('vendors'));
    }

    public function store(Request $request){

        $vendor = VendorAdjustment::create([
            "vendor_id" => $request->vendor_id,
            "date" => $request->date,
            "type" => $request->type,
            "rate" => $request->rate,
            "description" => $request->description,

        ]);

        return redirect()->route('vendor-adjustments.index')->with('success','Created Successfully');
    }

    public function edit($id)
    {
        $vendors = Vendor::all();
        $module = VendorAdjustment::where('id',$id)->first();

        return view('pages.vendor-adjustments.edit',compact('vendors','module'));
    }

    public function update(Request $request ,$id){

        $module = VendorAdjustment::Find($id);

        $module->vendor_id = $request->vendor_id;
        $module->date = $request->date;
        $module->type = $request->type;
        $module->rate = $request->rate;
        $module->description = $request->description;
        $module->update();

        return view('pages.vendor-adjustments.index')->with('success','Updated Successfully');
    }

    public function destroy($id)
    {
        
        try {
            $item = VendorAdjustment::findOrFail($id);
           
            $item->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}
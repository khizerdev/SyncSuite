<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Gate;
use Auth;
use Yajra\Datatables\Datatables;

class StockAdjustmentController extends Controller
{
    
   /*
    * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        if ($request->ajax()){

            $data = StockAdjustment::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('date', function($row){
                        $btn = \Carbon\Carbon::parse($row->date)->format('M-d-Y');
                        return $btn;
                    })
                    ->addColumn('product', function($row){
                        $btn = $row->product->name;
                        return $btn;
                    })
                    ->addColumn('qty', function($row){
                        $btn = round($row->qty,2);
                        return $btn;
                    })
                    ->addColumn('action', function($row){
                        $editUrl = route('stock-adjustments.edit', $row->id);
                        
                        $btn = '<a href="'.$editUrl.'" class="edit btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        
                        // $btn = $edit;
                        return $btn;
                    })
                    ->rawColumns(['action','product'])
                    ->make(true);
        }

         return view('pages.stock-adjustments.index');

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.stock-adjustments.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'product_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

        // try {

            $stockadjustment = StockAdjustment::create([
                'rate' => $request->rate,
                'qty' => $request->qty,
                "product_id" => $request->product_id,
                "date" => $request->date,
                "type" => $request->type,
                "details" => $request->details,
            ]);
        
           return redirect()->route('stock-adjustments.index')->with('success','Created Successfully');
        
        // } catch (\Throwable $th) {
            //  return back()->with('warning','Error');
        // }
    
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = StockAdjustment::Find($id); 
        return view('pages.stock-adjustments.edit',compact('module'));
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $module = StockAdjustment::Find($id);
        $request->validate([
            'product_id' => 'required',
            'rate' => 'required',
            'qty' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

        $module->product_id = $request->product_id;
        $module->rate = $request->rate;
        $module->qty = $request->qty; 
        $module->date = $request->date;
        $module->details = $request->details;
        $module->type = $request->type;
        $module->save();
        
        return back()->with('success','Updated Successfully');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
       try {
        $module = StockAdjustment::findOrFail($id);
        $module->delete();

        return response()->json(['message' => 'Deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
    }
            
    }
       
}
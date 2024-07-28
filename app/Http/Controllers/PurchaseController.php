<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Auth;
use App\Models\Purchase;
use App\Models\PurchaseOrderItems as ModelsPurchaseOrderItems;
use App\Models\Vendor;
use Yajra\Datatables\Datatables;
use App\PurchaseOrderItems;
use Carbon\Carbon;
use DateTime;

class PurchaseController extends Controller
{

   /*** Display a listing of the resource */
    public function index(Request $request)
    {
    
        // if(Auth::user()->role->name == 'super-admin' || in_array('purchase-orders-list',Auth::user()->permissions())){

        if ($request->ajax()) {

            $data = Purchase::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('vendor', function($row){
                        $btn = $row->vendor->name;
                         return $btn;
                    })
                    ->addColumn('date', function($row){
                        $date = new DateTime($row->date);
                        $btn= $date->format('M-d-Y');
                         return $btn;
                    })
                    ->addColumn('action', function($row){
                        
                        $delete = "<a href=".route('purchases.destroy',$row->id)." class='px-1' title='Delete'><i class='px-1 text-danger fa-2x fas fa-window-close'></i></a>";
                        
                        
                        $edit = "<a href=".route('purchases.edit',$row->id)." title='Edit'> <i class='fas fa-edit fa-2x' aria-hidden='true'></i></a>"; 
                         
                        $view = "<a href=".route('purchases.view',$row->id)." title='View'> <i class='fas fa-eye fa-2x text-warning' aria-hidden='true'></i></a>"; 
                        $btn = $edit.$delete.$view;
                        return $btn;
                    
                    })
                    ->rawColumns(['action'])
                    ->make(true);
                    
        }

         $modules = Purchase::all();
         return view('pages.purchases.index',compact('modules'));
        // }else{
        //     return back()->with('error',"you don't have permission for this action ");
        // };
    }

 
   /*** Show the form for creating a new resource */
    public function create()
    {
        
        $modules = Purchase::all();
        $products = Product::all();
        $vendors = Vendor::all();
        
        return view('pages.purchases.create',compact('modules','products','vendors'));
    }


    /*** Store a newly created resource in storage */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
        ]);
        
        $date = Carbon::createFromFormat('Y-m-d',$request->date);
        $last = Purchase::whereYear('date', date($date->format('Y')))->whereMonth('date',date($date->format('m')))->orderBy('serial', 'DESC')->first();
        if($last == null){
            $last = 1;
        }else{
            $last = $last->serial + 1;
        }
        
        $serial = str_pad(intval($last), 3, '0', STR_PAD_LEFT);
        $date = $date->format('ym');
        $serial_no = 'PO-'.$date.$serial;
        
        
        
        // try {
            
            $purchase = Purchase::create([
              'vendor_id' => $request->vendor_id,
              "serial_no" => $serial_no,
              "serial" => $last,
              'date' => $request->date,
            ]);
            
            if($request->has('items')){
                foreach($request->items as $item){
                    ModelsPurchaseOrderItems::create([
                       "product_id" => $item['id'],
                       "purchase_id" => $purchase->id,
                       "qty" => $item['qty'],
                    ]);
                }        
            }
        
              return redirect()->route('purchases.index')->with('success','Created Successfully');
        // }
        
        //catch exception
        // catch(Exception $e) {
        //     return redirect()->route('purchases.index')->with('warning','Error Found Contact To Admin');
        // }
  
    }


     /** * Show the form for editing the specified resource **/
    public function edit($id)
    {
        $module = Purchase::find($id); 
        $types = Type::all();
        $products = Product::all();
        
        return view('purchases.edit',compact('module','types','products'));
    }


     /*** Update the specified resource in storage ***/
    public function update(Request $request,$id)
    {
        $purchase = Purchase::Find($id);
        $number = intval($request->serial_no);
        $serial = str_pad(intval($request->serial_no), 3, '0', STR_PAD_LEFT);
        $date = Carbon::createFromFormat('Y-m-d',$request->date)->format('ym');
        $serial_no = 'PO-'.$date.$serial;
        
        
        $request->merge(['serial_no' => $serial_no]);
        $request->validate([
            'serial_no' => 'required|unique:purchases,serial_no,'.$purchase->id,
            'date' => 'required',
        ]);
        
        
        if($request->has('items')){
            $items = $request->items;
        }else{
            $items = [];
        } 
        
        $purchase->serial = $number;
        $purchase->serial_no = $request->serial_no;
        $purchase->vendor_id = $request->vendor_id;
        $purchase->date = $request->date;
        $purchase->save();
        
        $notDeleted = [];
        foreach($items as $item){
            
              if (array_key_exists("id",$item)){
                  $PurchaseOrderItems = PurchaseOrderItems::find($item['id']);
                  $PurchaseOrderItems->update([
                     "qty" => $item['qty'],    
                  ]);
              }else{
                 $PurchaseOrderItems = PurchaseOrderItems::create([
                  "product_id" => $item['product_id'],
                  "purchase_id" => $id,
                  "qty" => $item['qty'],
                 ]);
              }
              
            array_push($notDeleted,$PurchaseOrderItems->id);
        } 
        
        PurchaseOrderItems::where('purchase_id',$id)->whereNotIn('id', $notDeleted)->delete();
        return back()->with('success','Updated');
    }


    /*** Remove the specified resource from storage ***/
    public function delete($id)
    {
        $module = Purchase::Find($id);
        try {
             $module->delete();
             return redirect()->route('purchases.index')->with('success','Deleted');

        } catch (\Throwable $th) {
            return redirect()->route('purchases.index')->with('warning','Can Not Delete Becaouse The Data Used Some Where');
        }


    }

    public function view($id)
    {
        $module = Purchase::find($id); 
        return view('pages.purchases.view',compact('module'));

    }
    
        
}
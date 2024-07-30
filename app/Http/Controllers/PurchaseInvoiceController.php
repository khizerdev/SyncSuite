<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItems;
use App\Models\PurchaseReceipt;
use App\Models\Vendor;
use Illuminate\Support\Facades\Gate;
use Auth;
use Yajra\Datatables\Datatables;
use PDF;
use Carbon\Carbon;

class PurchaseInvoiceController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request )
    {
        
        if ($request->ajax()) {
            $data = Vendor::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        
                        $create = "<a class='mr-1 btn btn-success' href=".route('purchase-invoice.create',$row->id)." >Create</a>";
                        
                        
                        $manage = "<a href=".route('purchase-invoice.vendor_invoices',$row->id)." class='btn btn-primary px-1' >Manage</a>"; 
                        
                        $btn = $create.$manage;
                        return $btn;
                    
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
         return view('pages.purchase-invoices.vendors');

        
    }


 
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request,$id)
    {   

        $vendor = Vendor::find($id);
        if($request->ajax()){
        
               $vendor = Vendor::find($id);
               $purchases = Purchase::where('vendor_id',$id)->get();
                
               $data = [];
               foreach ($purchases as $purchase){
                  if($purchase->receipt){
                      foreach($purchase->receipt->items as $purchaseItem){
                        if($purchaseItem->invoice == false){
                            array_push($data,$purchase->receipt->id);     
                        }
                      }
                  }
               }
                
                $receipt = PurchaseReceipt::whereIn('id',$data);

                return Datatables::of($receipt)
                ->addIndexColumn()
                ->addColumn('date', function($row){
                    
                    $btn = $row->created_at->format('M-d-Y');
                    return $btn;
                })
                ->addColumn('action', function($row){
                    
                    $btn = "<input class='invoice_checks form-control' type='checkbox' value='".$row['id']."' />";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.purchase-invoices.create',compact('vendor'));
    }
    
    
      /**
     * Show the form for creating a new resource.
     */
    public function add(Request $request)
    {   
        if(! $request->has('receipt_id')){
            return back()->with('error','receipt Not Found');
        }
        
        if(! $request->has('vendor_id')){
            return back()->with('error','Vendor Not Found');
        }
         
        $receipt = explode(',',$request->receipt_id);
        $vendor =  Vendor::find($request->vendor_id);
        $receipts = PurchaseReceipt::whereIn('id',$receipt)->get(); 
         
      return view('pages.purchase-invoices.add',compact('receipts','vendor'));
    }


      /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        
         $request->validate([
            'date' => 'required',
        ]);
        
        $date = Carbon::createFromFormat('Y-m-d',$request->date);
        $last = PurchaseInvoice::whereYear('date', date($date->format('Y')))->whereMonth('date',date($date->format('m')))->orderBy('serial', 'DESC')->first();
        if($last == null){
            $last = 1;
        }else{
            $last = $last->serial + 1;
        }
        
        $serial = str_pad(intval($last), 3, '0', STR_PAD_LEFT);
        $date = $date->format('ym');
        $serial_no = 'PI-'.$date.$serial;
        
        $invoice = PurchaseInvoice::create([
            "serial_no" => $serial_no,
            "serial" => $last,
            'due_date' => $request->due_date,
            'date' => $request->date,
            'cartage' => $request->cartge,
        ]);
         
         foreach ($request->items as $rr) {
             PurchaseInvoiceItems::create([
              "gst" => $rr['gst'],
              "receipt_item_id" => $rr['id'],
              "invoice_id" => $invoice->id,
             ]);
         }
         
         return redirect()->route('purchase-invoice.view',$invoice->id)->with('success','Purchase Invoice Generated');
    }


    public function vendor_invoices(Request $request,$id)
    {
        $vendor = Vendor::find($id);
        if($request->ajax()){
        
            $vendor = Vendor::find($id);
            $purchases = Purchase::where('vendor_id',$id)->get();
    
            $data = [];
             foreach($purchases as $purchase){
                 if($purchase->receipt){
                     foreach($purchase->receipt->items as $purchaseItem){
                        if($purchaseItem->invoice){
                             array_push($data,$purchaseItem->invoice->invoice_id);      
                        }
                     }
                 }
             }
             

             $purchaseInvoice = purchaseInvoice::whereIn('id',$data);
             return Datatables::of($purchaseInvoice)
             ->addIndexColumn()
             ->addColumn('date', function($row){         
                $btn = $row->date->format('M-d-Y');
                return $btn;
            })
             ->addColumn('action', function($row){
                 
                 $btn = "<a href='".route('purchase-invoice.edit',$row->id)."' ><i class='fas fa-edit fa-2x'></i></a><a href='".route('purchase-invoice.view',$row->id)."' ><i class='fas fa-eye fa-2x text-warning '></i></a><a class='px-2 text-danger' href='".route('purchase-invoice.destroy',$row->id)."' ><i class='fas fa-window-close fa-2x'></i></a>";
                 return $btn;
             })

             ->addColumn('action', function($row){
                
                $delete = "<a href=".route('purchase-invoice.destroy',$row->id)." class='px-1' title='Delete'><i class='px-1 text-danger fa-2x fas fa-window-close'></i></a>";
                
                
                $edit = "<a href=".route('purchase-invoice.edit',$row->id)." title='Edit'> <i class='fas fa-edit fa-2x' aria-hidden='true'></i></a>"; 
                
                $view = "</a><a href='".route('purchase-invoice.view',$row->id)."' ><i class='fas fa-eye fa-2x text-warning '></i></a>"; 
                $btn = $edit.$delete.$view;
                return $btn;
            
            })
             ->rawColumns(['action'])
             ->make(true);
        }

        return view('pages.purchase-invoices.vendorInvoices',compact('vendor'));
    }


     /**
     * Show the form For
     */
    public function pdf($id)
    {   

        $invoice = PurchaseInvoice::Find($id);
        $data = [
            'invoice' => $invoice,
        ];

        // $dompdf = PDF::loadView('admin.purchase-invoices.pdf', $data)->setPaper('A3', 'portrait');
        // return $dompdf->stream('invoice.pdf',array('Attachment' => 0));

        return view('admin.purchase-invoices.pdf',compact('invoice'));
    }

    
     /**
     * Show the form For
     */
    public function edit($id)
    {   
        
        $invoice = PurchaseInvoice::Find($id);
        return view('pages.purchase-invoices.edit',compact('invoice'));
    }
    
      /**
     * Show the form For
     */
    public function update(Request $request,$id)
    {   
        $invoice = PurchaseInvoice::Find($id);
        $number = intval($request->serial_no);
    	$serial = str_pad(intval($request->serial_no), 3, '0', STR_PAD_LEFT);
    	$date = Carbon::createFromFormat('Y-m-d',$request->date)->format('ym');
    	$serial_no = 'SI-'.$date.$serial;
    	
    	$request->merge(['serial_no' => $serial_no]);
    	$request->validate([
    		'serial_no' => 'required|unique:purchase_invoices,serial_no,'.$invoice->id,
    		'date' => 'required',
    	]);
    	
    	$invoice->serial = $number;
        $invoice->serial_no = $request->serial_no;
        $invoice->due_date = $request->due_date;
        $invoice->date = $request->date;
        $invoice->cartage = $request->cartge;
        $invoice->save();
        
        foreach ($request->items as $rr) {
             $PurchaseInvoiceItems = PurchaseInvoiceItems::find($rr['id']);
             $PurchaseInvoiceItems->gst = $rr['gst'];
             $PurchaseInvoiceItems->save();                 
        }
        return back()->with('success','Updated');
    }
    
      /**
     * Show the form For
     */
    public function view($id)
    {   
        $invoice = PurchaseInvoice::Find($id);
        return view('pages.purchase-invoices.view',compact('invoice'));
    }


    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $module = PurchaseInvoice::Find($id);
        try {
            //  $module->delete();
             return redirect()->route('purchase-invoice.index')->with('success','Deleted');
     
            } catch (\Throwable $th) {
            return redirect()->route('purchase-invoice.index')->with('warning','Can Not Delete Becaouse The Data Used Some Where');
        }   
    }
    
    
}
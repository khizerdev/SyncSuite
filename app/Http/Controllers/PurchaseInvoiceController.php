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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PurchaseInvoice::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $view = "<a href=".route('purchase-invoice.view', $row->id)." class='btn btn-info mr-1'>View</a>";
                        $edit = "<a href=".route('purchase-invoice.edit', $row->id)." class='btn btn-warning mr-1'>Edit</a>";
                        return $view.$edit;
                    })
                    ->editColumn('date', function($row) {
                        return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
                    })
                    ->editColumn('due_date', function($row) {
                        return $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d-m-Y') : 'N/A';
                    })
                    ->editColumn('cartage', function($row) {
                        return number_format($row->cartage, 2);
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('pages.purchase-invoices.index');
    }


 
    /**
     * Show the form for creating a new resource.
     */
   public function create()
{
    // Get receipts that have items not present in purchase_invoice_items
    $receipts = PurchaseReceipt::whereHas('items', function($query) {
        $query->whereNotIn('id', function($subQuery) {
            $subQuery->select('receipt_item_id')
                    ->from('purchase_invoice_items');
        });
    })->get();

    return view('pages.purchase-invoices.create', compact('receipts'));
}

public function add(Request $request)
{   
    $request->validate([
        'receipt_id' => 'required|exists:purchase_receipts,id'
    ]);

    // Get receipt with items that haven't been invoiced
    $receipt = PurchaseReceipt::with(['items' => function($query) {
        $query->whereNotIn('id', function($subQuery) {
            $subQuery->select('receipt_item_id')
                    ->from('purchase_invoice_items');
        });
    }])->findOrFail($request->receipt_id);

    if($receipt->items->isEmpty()) {
        return response()->json(['error' => 'All items in this receipt have already been invoiced'], 400);
    }

    return view('pages.purchase-invoices.add', ['receipts' => collect([$receipt])])->render();
}

// store method remains the same as before

public function store(Request $request)
{
    $request->validate([
        'date' => 'required',
        'receipt_id' => 'required|exists:purchase_receipts,id'
    ]);
    
    $date = Carbon::createFromFormat('Y-m-d', $request->date);
    $last = PurchaseInvoice::whereYear('date', $date->format('Y'))
                          ->whereMonth('date', $date->format('m'))
                          ->orderBy('serial', 'DESC')
                          ->first();
    
    $serial = ($last) ? $last->serial + 1 : 1;
    $serialNo = 'PI-'.$date->format('ym').str_pad($serial, 3, '0', STR_PAD_LEFT);
    
    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        // Either use move() OR store(), not both
        // Option 1: Using move()
        $fileName = uniqid() . '.' . $request->file('attachment')->getClientOriginalExtension();
        $request->file('attachment')->move(public_path('attachments'), $fileName);
        $attachmentPath = 'attachments/' . $fileName;
        
        // OR Option 2: Using store() (preferred for Laravel)
        // $attachmentPath = $request->file('attachment')->store('attachments', 'public');
    } else {
        $attachmentPath = null;
    }
    
    // Create the invoice
    $invoice = PurchaseInvoice::create([
        "serial_no" => $serialNo,
        "serial" => $serial,
        'due_date' => $request->due_date,
        'date' => $request->date,
        'cartage' => $request->cartge,
        'descr' => $request->descr,
        'attachmentPath' => $attachmentPath,
    ]);
     
    // Add items to the invoice
    foreach ($request->items as $item) {
        PurchaseInvoiceItems::create([
            "gst" => $item['gst'],
            "receipt_item_id" => $item['id'],
            "invoice_id" => $invoice->id,
        ]);
    }
     
    return redirect()->route('purchase-invoice.index', $invoice->id)
                    ->with('success', 'Purchase Invoice Generated');
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
        $invoice->descr = $request->descr;
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
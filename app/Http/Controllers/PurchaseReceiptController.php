<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReceipt;
use Illuminate\Support\Facades\Gate;
use Auth;
use App\Product;
use App\PurchaseInvoice;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class PurchaseReceiptController extends Controller
{

    /*
     * Display a listing of the resource.
    */
    public function index(Request $request)
    {

        if ($request->ajax())
        {
            $data = PurchaseReceipt::select('*');
            return Datatables::of($data)->addIndexColumn()->addColumn('date', function ($row)
            {
                $btn = $row
                    ->date
                    ->format('M-d-Y');
                return $btn;
            })->addColumn('vendor', function ($row)
            {
                if ($row->purchase)
                {
                    $btn = "<a href=" . route('vendors.edit', $row
                        ->purchase
                        ->vendor
                        ->id) . ">" . $row
                        ->purchase
                        ->vendor->name . "</a>";
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })->addColumn('purchase', function ($row)
            {
                if ($row->purchase)
                {
                    $btn = "<a href=" . route('purchases.edit', $row->purchase_id) . " >#" . $row
                        ->purchase->serial_no . "</a>";
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })->addColumn("action", function ($row)
            {
                $delete = ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm" title="Delete"><i class="px-1 text-danger fas fa-window-close text-white"></i></button>';

                $edit = "<a href=" . route('purchase-receipts.edit', $row->id) . " title='Edit' class='btn btn-primary btn-sm mr-1'> <i class='fas fa-edit text-white' aria-hidden='true'></i></a>";

                $view = "<a title='View' class='ml-1 btn btn-warning btn-sm' href=" . route('purchase-receipts.view', $row->id) . "class='px-1'><i class='fas fa-eye text-white'></i></a>";
                $btn = $edit . $delete . $view;
                return $btn;
            })->rawColumns(['action', 'purchase', 'vendor'])
                ->make(true);
        }

        return view('pages.purchase-receipts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = PurchaseReceipt::all();
        $purchases = Purchase::all();

        return view('pages.purchase-receipts.create', compact('modules', 'purchases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate(['date' => 'required', ]);

        $date = Carbon::createFromFormat('Y-m-d\TH:i', $request->date);
        $last = PurchaseReceipt::whereYear('date', date($date->format('Y')))
            ->whereMonth('date', date($date->format('m')))
            ->orderBy('serial', 'DESC')
            ->first();
        if ($last == null)
        {
            $last = 0;
        }
        else
        {
            $last = $last->serial + 1;
        }

        $serial = str_pad(intval($last) , 3, '0', STR_PAD_LEFT);
        $date = $date->format('ym');
        $serial_no = 'PR-' . $date . $serial;

        if ($request->has('items'))
        {
            $items = $request->items;
        }
        else
        {
            $items = [];

        }
      
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
    
        $receipt = PurchaseReceipt::create([
            'date' => $request->date,
            'descr' => $request->descr, 
            'purchase_id' => $request->purchase_id, 
            "serial_no" => $serial_no, 
            "serial" => $last, 
            "party_challan" => $request->party_challan,
            'attachmentPath' => $attachmentPath,
            ]);

        foreach ($items as $key => $value)
        {
            PurchaseItem::create(["product_id" => $value['id'], "receipt_id" => $receipt->id, "qty" => $value['qty'], "rqty" => $value['rqty'],  "rate" => $value['rate'], "total" => $value['rqty'] * $value['rate'], ]);
        }

        return redirect()->route('purchase-receipts.index')
            ->with('success', 'Created Successfully');
    }

    /**
     */
    public function edit($id)
    {
        $receipt = PurchaseReceipt::find($id);
        return view('pages.purchase-receipts.edit', compact('receipt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // dd($request->all());
        $module = PurchaseReceipt::find($id);

        $number = intval($request->serial_no);
        $serial = str_pad(intval($request->serial_no) , 3, '0', STR_PAD_LEFT);
        $date = Carbon::createFromFormat('Y-m-d\TH:i', $request->date)->format('ym');
        
        
        $serial_no = 'PR-' . $date . $serial;

        $request->merge(['serial_no' => $serial_no]);
        $request->validate(['serial_no' => 'required|unique:purchase_receipts,serial_no,' . $module->id, 'date' => 'required', ]);

        if ($request->has('items'))
        {
            $items = $request->items;
        }
        else
        {
            $items = [];
        }

        $module->serial = $number;
        $module->serial_no = $request->serial_no;
        $module->date = $request->date;
        $module->descr = $request->descr;
        $module->party_challan = $request->party_challan;
        $module->save();

        $notDeletedItem = [];
        foreach ($items as $key => $value)
        {

            $purchaseItem = PurchaseItem::find($value['id']);
            if ($purchaseItem != null)
            {

                $purchaseItem->qty = $value['qty'];
                $purchaseItem->rqty = $value['rqty'];
                $purchaseItem->rate = $value['price'];
                $purchaseItem->total = $value['rqty'] * $value['price'];
                $purchaseItem->save();
                array_push($notDeletedItem, $purchaseItem->id);

            }
        }

        $rec = PurchaseItem::where('receipt_id', $module->id);
        $rec = $rec->whereNotIn('id', $notDeletedItem);
        $rec->delete();

        return back()
            ->with('success', 'Updated');
    }

    public function view($id)
    {

        $receipt = PurchaseReceipt::find($id);
        return view('purchase-receipts.view', compact('receipt'));

    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $branch = PurchaseReceipt::Find($id);
            $branch->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }

    }

}


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
use Carbon\Carbon;
use DateTime;

class PurchaseController extends Controller
{
    /*** Display a listing of the resource */
    public function index(Request $request)
    {
        // if(Auth::user()->role->name == 'super-admin' || in_array('purchase-orders-list',Auth::user()->permissions())){
        if ($request->ajax())
        {
            $data = Purchase::select("*");
            return Datatables::of($data)->addIndexColumn()->addColumn("vendor", function ($row)
            {
                $btn = $row
                    ->vendor->name;
                return $btn;
            })->addColumn("date", function ($row)
            {
                $date = new DateTime($row->date);
                $btn = $date->format("M-d-Y");
                return $btn;
            })->addColumn("action", function ($row)
            {
                $delete = ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm" title="Delete"><i class="px-1 text-danger fas fa-window-close text-white"></i></button>';

                $edit = "<a href=" . route('purchases.edit', $row->id) . " title='Edit' class='btn btn-primary btn-sm mr-1'> <i class='fas fa-edit text-white' aria-hidden='true'></i></a>";

                $view = "<a title='View' class='ml-1 btn btn-warning btn-sm' href=" . route('purchases.view', $row->id) . "class='px-1'><i class='fas fa-eye text-white'></i></a>";
                $btn = $edit . $delete . $view;
                return $btn;
            })->rawColumns(["action"])
                ->make(true);
        }

        $modules = Purchase::all();
        return view("pages.purchases.index", compact("modules"));
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

        return view("pages.purchases.create", compact("modules", "products", "vendors"));
    }

    /*** Store a newly created resource in storage */
    public function store(Request $request)
    {
        $request->validate(["date" => "required", ]);

        $date = Carbon::createFromFormat("Y-m-d", $request->date);
        $last = Purchase::whereYear("date", date($date->format("Y")))
            ->whereMonth("date", date($date->format("m")))
            ->orderBy("serial", "DESC")
            ->first();
        if ($last == null)
        {
            $last = 1;
        }
        else
        {
            $last = $last->serial + 1;
        }

        $serial = str_pad(intval($last) , 3, "0", STR_PAD_LEFT);
        $date = $date->format("ym");
        $serial_no = "PO-" . $date . $serial;

        // try {
        $purchase = Purchase::create(["vendor_id" => $request->vendor_id, "serial_no" => $serial_no, "serial" => $last, "date" => $request->date, ]);

        if ($request->has("items"))
        {
            foreach ($request->items as $item)
            {
                ModelsPurchaseOrderItems::create(["product_id" => $item["id"], "purchase_id" => $purchase->id, "qty" => $item["qty"], ]);
            }
        }

        return redirect()->route("purchases.index")
            ->with("success", "Created Successfully");
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
        $products = Product::all();
        $vendors = Vendor::all();

        return view("pages.purchases.edit", compact("module", "products", "vendors"));
    }

    /*** Update the specified resource in storage ***/
    public function update(Request $request, $id)
    {
        $purchase = Purchase::Find($id);
        $number = intval($request->serial_no);
        $serial = str_pad(intval($request->serial_no) , 3, "0", STR_PAD_LEFT);
        $date = Carbon::createFromFormat("Y-m-d", $request->date)
            ->format("ym");
        $serial_no = "PO-" . $date . $serial;

        $request->merge(["serial_no" => $serial_no]);
        $request->validate(["serial_no" => "required|unique:purchases,serial_no," . $purchase->id, "date" => "required", ]);

        if ($request->has("items"))
        {
            $items = $request->items;
        }
        else
        {
            $items = [];
        }

        $purchase->serial = $number;
        $purchase->serial_no = $request->serial_no;
        $purchase->vendor_id = $request->vendor_id;
        $purchase->date = $request->date;
        $purchase->save();

        $notDeleted = [];
        foreach ($items as $item)
        {
            if (array_key_exists("id", $item))
            {
                $PurchaseOrderItems = ModelsPurchaseOrderItems::find($item["id"]);
                $PurchaseOrderItems->update(["qty" => $item["qty"], ]);
            }
            else
            {
                $PurchaseOrderItems = ModelsPurchaseOrderItems::create(["product_id" => $item["product_id"], "purchase_id" => $id, "qty" => $item["qty"], ]);
            }

            array_push($notDeleted, $PurchaseOrderItems->id);
        }

        ModelsPurchaseOrderItems::where("purchase_id", $id)->whereNotIn("id", $notDeleted)->delete();
        return back()
            ->with("success", "Updated");
    }

    /*** Remove the specified resource from storage ***/
    public function destroy($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase->delete();
    
            return response()->json(['message' => 'Branch deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }

    public function view($id)
    {
        $module = Purchase::find($id);
        return view("pages.purchases.view", compact("module"));
    }
}


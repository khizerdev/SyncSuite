<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InwardGeneral;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class InwardGeneralController extends Controller
{
    
    public function index(Request $request)
    {

        if ($request->ajax())
        {
            $data = InwardGeneral::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumns(['ref_number','party','department','description'])
                ->addColumn('date', function ($row){
                    $btn = $row
                        ->date
                        ->format('M-d-Y');
                    return $btn;
                })
                ->addColumn('action', function ($row)
            {
                $delete = ' <button onclick="deleteRecord('.$row->id.')" class="delete btn btn-danger btn-sm" title="Delete"><i class="px-1 text-danger fas fa-window-close text-white"></i></button>';

                $edit = "<a href=" . route('inward-general.edit', $row->id) . " title='Edit' class='btn btn-primary btn-sm mr-1'> <i class='fas fa-edit text-white' aria-hidden='true'></i></a>";

                // $view = "<a title='View' class='ml-1 btn btn-warning btn-sm' href=" . route('inward-general.view', $row->id) . "class='px-1'><i class='fas fa-eye text-white'></i></a>";
                $btn = $edit . $delete;
                return $btn;

            })->rawColumns(['action'])
                ->make(true); 
        }

        return view('pages.inward-general.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.inward-general.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate(['date' => 'required', ]);

        $date = Carbon::createFromFormat('Y-m-d\TH:i', $request->date);
        $last = InwardReceipt::whereYear('date', date($date->format('Y')))
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

        $receipt = InwardReceipt::create(['date' => $request->date, 'due_date' => $request->due_date, 'purchase_id' => $request->purchase_id, "serial_no" => $serial_no, "serial" => $last, "party_challan" => $request->party_challan, ]);

        foreach ($items as $key => $value)
        {
            $purchaseItem = InwardItem::create(["product_id" => $value['id'], "receipt_id" => $receipt->id, "qty" => $value['qty'], "rate" => $value['rate'], "total" => $value['qty'] * $value['rate'], ]);
        }

        return redirect()->route('inward-receipts.index')
            ->with('success', 'Created Successfully');
    }

    public function edit($id)
    {
        $module = InwardGeneral::with('items')->findOrFail($id);
        return view('pages.inward-general.edit', compact('module'));
    }

    public function view($id)
    {

        $receipt = InwardReceipt::find($id);
        return view('inward-receipts.view', compact('receipt'));

    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $branch = InwardGeneral::findOrFail($id);
            $branch->delete();
    
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete', 'error' => $e->getMessage()], 500);
        }
    }
}


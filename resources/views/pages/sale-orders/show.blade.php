@extends('layouts.app')

@section('content')

<style>
    .sales-order {
        background-color: white;
        padding: 30px;
        max-width: 1200px;
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .header-title {
        text-align: center;
        font-weight: bold;
        font-size: 24px;
        margin-bottom: 5px;
    }
    .header-subtitle {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }
    .table {
        font-size: 14px;
    }
    .table thead th {
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
    }
    .table tbody td {
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
    .total-row {
        font-weight: bold;
        background-color: #f8f9fa;
    }
    .signature-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }
    .signature-line {
        border-top: 1px solid #000;
        width: 200px;
        text-align: center;
        padding-top: 5px;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header row align-items-center">
                        <div class="col-6">
                            <h3 class="card-title">View Sale Order</h3>
                        </div>
                        
                    </div>

                    <div class="card-body">
                        <div class="sales-order">
                            <div class="header-title">PARAMOUNT LACE</div>
                            <div class="header-subtitle">SALES ORDER</div>
                            
                            <table class="table table-bordered mb-3">
                                <tbody>
                                    <tr>
                                        <td width="15%"><strong>Customer</strong></td>
                                        <td width="45%">{{ $saleOrder->customer->name ?? 'N/A' }}</td>
                                        <td width="15%"><strong>Order #</strong></td>
                                        <td width="25%">{{ $saleOrder->sale_no ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Remarks</strong></td>
                                        <td>{{ $saleOrder->remarks ?? 'N/A' }}</td>
                                        <td><strong>Date</strong></td>
                                        <td>{{ $saleOrder->created_at ? \Carbon\Carbon::parse($saleOrder->created_at)->format('d-M-Y') : 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Product Name</th>
                                        <th>Color</th>
                                        <th>Than Qty</th>
                                        <th>Quantity</th>
                                        <th>Uom</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Stitch</th>
                                        <th>Total Stitch</th>
                                        <th>Spangle</th>
                                        <th>Total Spangle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalThanQty = 0;
                                        $totalQty = 0;
                                        $totalAmount = 0;
                                        $totalStitch = 0;
                                        $totalSpangle = 0;
                                    @endphp

                                    @foreach($saleOrder->items as $index => $item)
                                        @php
                                            $thanQty = $item->than_qty ?? 0;
                                            $qty = $item->quantity ?? 0;
                                            $rate = $item->rate ?? 0;
                                            $amount = $item->amount ?? ($qty * $rate);
                                            $stitch = $item->stitch ?? 0;
                                            $itemTotalStitch = $item->total_stitch ?? ($stitch * $qty);
                                            $spangle = $item->spangle ?? 0;
                                            $itemTotalSpangle = $item->total_spangle ?? ($spangle * $qty);
                                            
                                            $totalThanQty += $thanQty;
                                            $totalQty += $qty;
                                            $totalAmount += $amount;
                                            $totalStitch += $itemTotalStitch;
                                            $totalSpangle += $itemTotalSpangle;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->design->design_code ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $item->color->code ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($thanQty, 2) }}</td>
                                            <td class="text-right">{{ number_format($qty, 2) }}</td>
                                            <td class="text-center">{{ $item->design->unit_of_measure }}</td>
                                            <td class="text-right">{{ number_format($rate, 2) }}</td>
                                            <td class="text-right">{{ number_format($amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($stitch, 0) }}</td>
                                            <td class="text-right">{{ number_format($itemTotalStitch, 0) }}</td>
                                            <td class="text-right">{{ number_format($spangle, 0) }}</td>
                                            <td class="text-right">{{ number_format($itemTotalSpangle, 0) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr class="total-row">
                                        <td colspan="3" class="text-center"><strong>Total</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totalThanQty, 2) }}</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totalQty, 0) }}</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                                        <td></td>
                                        <td class="text-right"><strong>{{ number_format($totalStitch, 0) }}</strong></td>
                                        <td></td>
                                        <td class="text-right"><strong>{{ number_format($totalSpangle, 0) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mb-4">
                                <strong>IN WORDS:</strong> <em>{{ $amountInWords  }} ONLY</em>
                            </div>

                            <div class="signature-section">
                                <div>
                                    <div class="signature-line">SALES MANAGER</div>
                                </div>
                                <div>
                                    <div class="signature-line">CUSTOMER</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
    
@endsection

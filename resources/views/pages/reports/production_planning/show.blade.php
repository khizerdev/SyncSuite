@extends('layouts.app')

@section('content')

<style>
    .header-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .header-section h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .header-section h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .date-range {
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .production-date {
        text-align: left;
        margin-bottom: 15px;
        margin-top: 30px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .table-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    .custom-table {
        border: 2px solid #000;
    }
    .custom-table th {
        background-color: #fff;
        border: 1px solid #000;
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
        padding: 10px;
    }
    .custom-table td {
        border: 1px solid #000;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }
    .custom-table .text-left {
        text-align: left !important;
    }
    .custom-table .text-right {
        text-align: right !important;
    }
    .section-header {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .signature-section {
        margin-top: 40px;
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        padding: 0 50px;
    }
    .signature-line {
        text-align: center;
    }
    .signature-line hr {
        border-top: 2px solid #000;
        width: 200px;
        margin: 40px auto 10px;
    }
    .signature-line p {
        font-weight: 600;
        margin: 0;
    }
    .action-buttons {
        text-align: center;
        margin: 20px 0;
    }
    .page-break {
        page-break-after: always;
    }
    @media print {
        .action-buttons, .content-header {
            display: none;
        }
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Production Planning Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('production-planning.report') }}">Report</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="action-buttons">
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <a href="{{ route('production-planning.report') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Form
                            </a>
                        </div>

                        @if($productionPlannings->isEmpty())
                            <div class="alert alert-info text-center">
                                <h4>No Production Planning Found</h4>
                                <p>No production planning records exist for the selected date range.</p>
                            </div>
                        @else
                            <div class="container">
                                <div class="header-section">
                                    <h1>PARAMOUNT LACE</h1>
                                    <h2>Daily Production Planning Report</h2>
                                </div>

                                <div class="date-range">
                                    Date Range: {{ $startDate->format('d-M-Y') }} to {{ $endDate->format('d-M-Y') }}
                                </div>

                                @foreach($productionPlannings as $planningIndex => $productionPlanning)
                                    <div class="production-date">
                                        Production Date: <span class="ml-2">{{ \Carbon\Carbon::parse($productionPlanning->production_date)->format('d-M-Y') }}</span>
                                    </div>

                                    <div class="table-container">
                                        <div class="table-responsive">
                                            <table class="table custom-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Ref. No.</th>
                                                        <th>S.O No.</th>
                                                        <th>Party Name</th>
                                                        <th>Design</th>
                                                        <th>Color</th>
                                                        <th>Than<br>Qty</th>
                                                        <th>Lace's</th>
                                                        <th>Design<br>Stitch</th>
                                                        <th>Total<br>Stitch</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Machine --}}
                                                    <tr class="section-header">
                                                        <td colspan="10" class="text-left">
                                                            <strong>Machine: {{ $productionPlanning->machine->code ?? '-' }}</strong>
                                                        </td>
                                                    </tr>

                                                    {{-- Shift --}}
                                                    <tr class="section-header">
                                                        <td colspan="10" class="text-left">
                                                            <strong>Shift: {{ $productionPlanning->shift->name ?? 'A SHIFT' }}</strong>
                                                        </td>
                                                    </tr>

                                                    @php
                                                        $totalThanQty = 0;
                                                        $totalLaceQty = 0;
                                                        $totalDesignStitch = 0;
                                                        $totalStitch = 0;
                                                    @endphp

                                                    {{-- Loop through all related SaleOrderItems --}}
                                                    @foreach($productionPlanning->items as $index => $item)
                                                        @php
                                                            $refNo = $item->saleOrder->order_reference ?? '-';
                                                            $soNo = $item->saleOrder->sale_no ?? '-';
                                                            $party = $item->saleOrder->customer->name ?? '-';
                                                            $design = $item->design->design_code ?? '-';
                                                            $color = $item->color->title ?? '-';
                                                            $thanQty = $item->pivot->planned_qty ?? 0;
                                                            $laceQty = $item->pivot->planned_lace_qty ?? 0;
                                                            $designStitch = $item->design_stitch ?? 0;
                                                            $totalStitchValue = $item->total_stitch ?? 0;

                                                            $totalThanQty += $thanQty;
                                                            $totalLaceQty += $laceQty;
                                                            $totalDesignStitch += $designStitch;
                                                            $totalStitch += $totalStitchValue;
                                                        @endphp

                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $refNo }}</td>
                                                            <td>{{ $soNo }}</td>
                                                            <td class="text-left">{{ $party }}</td>
                                                            <td>{{ $design }}</td>
                                                            <td>{{ $color }}</td>
                                                            <td>{{ $thanQty }}</td>
                                                            <td>{{ $laceQty }}</td>
                                                            <td>{{ number_format($designStitch) }}</td>
                                                            <td>{{ number_format($totalStitchValue) }}</td>
                                                        </tr>
                                                    @endforeach

                                                    {{-- Totals --}}
                                                    <tr class="table-secondary">
                                                        <td colspan="6" class="text-right"><strong>Total:</strong></td>
                                                        <td><strong>{{ $totalThanQty }}</strong></td>
                                                        <td><strong>{{ $totalLaceQty }}</strong></td>
                                                        <td><strong>{{ number_format($totalDesignStitch) }}</strong></td>
                                                        <td><strong>{{ number_format($totalStitch) }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="signature-section">
                                        <div class="signature-line">
                                            <hr>
                                            <p>PREPARED BY</p>
                                        </div>
                                        <div class="signature-line">
                                            <hr>
                                            <p>APPROVED BY</p>
                                        </div>
                                    </div>

                                    {{-- Add page break if not the last item --}}
                                    @if(!$loop->last)
                                        <div class="page-break"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
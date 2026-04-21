@extends('layouts.app')

@section('title', 'Commission Bill #' . $commission->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h2 mb-0 text-gray-800"><i class="fas fa-file-invoice me-2 text-primary"></i>Commission Bill Details</h1>
    <div class="d-flex flex-wrap gap-2 shadow-sm">
        <a href="{{ route('salman_commissions.export_pdf', ['commission' => $commission->id, 'print' => 1]) }}" target="_blank" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-print me-1"></i>Print
        </a>
        <a href="{{ route('salman_commissions.export_pdf', ['commission' => $commission->id]) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-file-pdf me-1"></i>Download PDF
        </a>
        <a href="{{ route('salman_commissions.index') }}" class="btn btn-secondary shadow-sm text-white" style="border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow border-0 overflow-hidden">
            <div class="card-header bg-dark text-white p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0 text-uppercase tracking-wider">Commission Invoice</h4>
                        <div class="small opacity-75">Generated for {{ $commission->user->name }}</div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="h3 mb-0">#CB-{{ str_pad($commission->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="small opacity-75">{{ $commission->commission_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small font-weight-bold">To:</h6>
                        <h5 class="mb-1">{{ $commission->user->name }}</h5>
                        <div class="text-muted">{{ $commission->user->email }}</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted text-uppercase small font-weight-bold">Customer Reference:</h6>
                        <h5 class="mb-1">{{ $commission->customer->name ?? '-' }}</h5>
                        <div class="text-muted small">{{ $commission->customer->address ?? '' }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th style="width: 50px;">S#</th>
                                <th>Item Name</th>
                                <th>Bill #</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Comm. %</th>
                                <th class="text-end">Comm. Amt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sno = 1; @endphp
                            @foreach($commission->details as $detail)
                                @foreach($detail->bill->billItems as $item)
                                <tr>
                                    <td class="text-center">{{ $sno++ }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->item->name }}</div>
                                        <div class="small text-muted">{{ $item->remarks }}</div>
                                    </td>
                                    <td class="text-center small text-muted">{{ $detail->bill->bill_number }}</td>
                                    <td class="text-end">{{ number_format($item->quantity) }}</td>
                                    <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($item->total, 2) }}</td>
                                    <td class="text-center font-monospace">{{ number_format($detail->percentage, 2) }}%</td>
                                    <td class="text-end fw-bold">
                                        @php $itemComm = ($item->total * $detail->percentage) / 100; @endphp
                                        {{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($itemComm, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="h4">
                                <th colspan="7" class="text-end py-3">Total Payable Commission:</th>
                                <th class="text-end py-3 text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($commission->amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($commission->notes)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-muted text-uppercase small font-weight-bold mb-2">Remarks:</h6>
                    <p class="text-dark">{{ $commission->notes }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light p-4 text-center">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <div class="small text-muted mb-1">Status</div>
                        <span class="badge bg-success px-3">Generated</span>
                    </div>
                    <div class="col-md-4 border-end">
                        <div class="small text-muted mb-1">Reference</div>
                        <div class="fw-bold">{{ $commission->reference ?: 'System Generated' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted mb-1">Items Included</div>
                        <div class="fw-bold">{{ $commission->details->count() }} Bills</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: #fff !important; }
    .btn, .nav-link, .d-flex.align-items-center.justify-content-between.mb-4 { display: none !important; }
    .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
    .card-header { background-color: #343a40 !important; color: #fff !important; }
    .container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
}
</style>
@endsection

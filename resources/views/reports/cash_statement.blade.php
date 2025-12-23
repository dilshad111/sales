@extends('layouts.app')

@section('title', 'Cash Statement')

@section('content')
<h1><i class="fas fa-cash-register me-2"></i>Cash Statement</h1>

<a href="{{ route('reports.cash_statement_pdf', request()->query()) }}" class="btn btn-primary mb-3"><i class="fas fa-download me-1"></i>Download PDF</a>

<form method="GET" class="mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="start_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
        </div>
    </div>
</form>

@foreach($reports as $report)
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <span><i class="fas fa-user me-2"></i>{{ $report['customer']->name }}</span>
        <span><i class="fas fa-phone small me-1"></i>{{ $report['customer']->phone }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 5%">S.No</th>
                        <th class="text-center align-middle" style="width: 12%">Date</th>
                        <th class="text-center align-middle" style="width: 15%">Payment Mode</th>
                        <th class="align-middle">Payment Description (Remarks)</th>
                        <th class="text-center align-middle" style="width: 15%">Payment Party</th>
                        <th class="text-end align-middle" style="width: 15%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['payments'] as $idx => $payment)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ ucfirst($payment->mode) }}</td>
                        <td>{{ $payment->remarks ?: '-' }}</td>
                        <td class="text-center">{{ $payment->paymentParty->name ?? '-' }}</td>
                        <td class="text-end">₨{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-2 text-muted">No payments received in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light text-end">
                    <tr>
                        <th colspan="5">Sub Total:</th>
                        <th class="text-end">₨{{ number_format($report['subtotal'], 2) }}</th>
                    </tr>
                    <tr class="text-danger">
                        <th colspan="5">Outstanding:</th>
                        <th class="text-end">₨{{ number_format($report['outstanding'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endforeach

<div class="card border-primary mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>SUMMARY</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 border-end">
                <p class="h6 mb-3">General Summary</p>
                <div class="d-flex justify-content-between mb-2">
                    <span>Received Total Amount:</span>
                    <span class="fw-bold text-success">₨{{ number_format($totalReceived, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Amount Receivable:</span>
                    <span class="fw-bold text-danger">₨{{ number_format($totalReceivable, 2) }}</span>
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <p class="h6 mb-3">Payment Party wise summary</p>
                @foreach($partySummary as $pSummary)
                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                    <span>{{ $pSummary['name'] }}:</span>
                    <span class="fw-bold">
                        @if($pSummary['amount'] > 0)
                            ₨{{ number_format($pSummary['amount'], 2) }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

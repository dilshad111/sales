@extends('layouts.app')

@section('title', 'Cash Statement')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.sales') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Cash Statement</li>
                    </ol>
                </nav>
                <h3 class="h4 mb-0 fw-bold text-gray-800 d-flex align-items-center">
                    <span class="bg-info bg-opacity-10 p-2 rounded-3 me-2">
                        <i class="fas fa-cash-register text-info fa-sm"></i>
                    </span>
                    Cash Statement
                </h3>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('reports.cash_statement_pdf', array_merge(request()->query(), ['print' => 1])) }}" target="_blank" class="btn btn-sm px-4 py-2 shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-print me-2"></i>Print
                </a>
                <a href="{{ route('reports.cash_statement_pdf', request()->query()) }}" class="btn btn-sm px-4 py-2 shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                </a>
                <a href="{{ route('reports.cash_statement_excel', request()->query()) }}" class="btn btn-sm px-4 py-2 shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
        </div>
    </div>
</div>

<form method="GET" class="mb-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-calendar-alt me-1"></i>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-calendar-alt me-1"></i>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100 fw-bold"><i class="fas fa-filter me-1"></i>Apply Filters</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #28a745 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;">Total Collection</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800 text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceived, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #17a2b8 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.75rem;">Highest Party Collection</div>
                @php $maxParty = collect($partySummary)->sortByDesc('amount')->first(); @endphp
                <div class="h4 mb-0 font-weight-bold text-gray-800 text-nowrap">
                    {{ $maxParty ? ($companySetting->currency_symbol ?? 'Rs.') . ' ' . number_format($maxParty['amount'], 2) : '0.00' }}
                </div>
                <div class="small text-muted mt-1">{{ $maxParty['name'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #fffafa 100%); border-radius: 12px; border-left: 5px solid #dc3545 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.75rem;">Outstanding Balance</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800 text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceivable, 2) }}</div>
            </div>
        </div>
    </div>
</div>

@forelse($reports as $report)
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 text-white fw-bold"><i class="fas fa-user-circle me-2 text-info"></i>{{ $report['customer']->name }}</h6>
        <div class="small text-light">
            <i class="fas fa-phone-alt me-1 text-info"></i> {{ $report['customer']->phone }}
            <span class="mx-2">|</span>
            <i class="fas fa-envelope me-1 text-info"></i> {{ $report['customer']->email }}
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-center" style="width: 5%">S.No</th>
                        <th class="text-center" style="width: 12%">Date</th>
                        <th class="text-center" style="width: 12%">Mode</th>
                        <th>Description (Remarks)</th>
                        <th class="text-center">Collector Party</th>
                        <th class="text-end pe-4" style="width: 15%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['payments'] as $idx => $payment)
                    <tr>
                        <td class="ps-4 text-center text-muted small">{{ $idx + 1 }}</td>
                        <td class="text-center text-dark fw-bold text-nowrap">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill fw-bold {{ $payment->mode == 'cash' ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary' }}" style="font-size: 0.7rem; border: 1px solid currentColor;">
                                <i class="fas {{ $payment->mode == 'cash' ? 'fa-wallet' : 'fa-university' }} me-1"></i>
                                {{ strtoupper($payment->mode) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $payment->remarks ?: '—' }}</td>
                        <td class="text-center fw-semibold text-secondary small">{{ $payment->paymentParty->name ?? '-' }}</td>
                        <td class="text-end pe-4 fw-bold text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light border-top">
                    <tr>
                        <td colspan="5" class="text-end fw-bold text-uppercase small text-muted">Total Received:</td>
                        <td class="text-end pe-4 fw-bold text-primary h5 mb-0 text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($report['subtotal'], 2) }}</td>
                    </tr>
                    @if($report['outstanding'] > 0)
                    <tr>
                        <td colspan="5" class="text-end fw-bold text-uppercase small text-muted">Balance Due:</td>
                        <td class="text-end pe-4 fw-bold text-danger text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($report['outstanding'], 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <div class="mb-3">
        <i class="fas fa-folder-open text-muted opacity-25" style="font-size: 5rem;"></i>
    </div>
    <h5 class="text-muted">No cash transactions found for the selected period.</h5>
</div>
@endforelse

<div class="card border-0 mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);">
        <h5 class="mb-0 text-white d-flex align-items-center fw-bold">
            <span class="bg-info bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                <i class="fas fa-briefcase text-info fa-sm"></i>
            </span>
            Consolidated Collection Summary
        </h5>
    </div>
    <div class="card-body py-4">
        <div class="row g-4 h-100">
            {{-- General Summary --}}
            <div class="col-lg-5">
                <h6 class="text-uppercase fw-bold text-muted ls-1 mb-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">
                    <i class="fas fa-info-circle me-1"></i>Grand Totals
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                        <div class="text-muted small fw-semibold">Net Collection</div>
                        <div class="h3 mb-0 fw-bold text-success text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceived, 2) }}</div>
                    </div>
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-danger">
                        <div class="text-muted small fw-semibold">Net Outstanding</div>
                        <div class="h3 mb-0 fw-bold text-danger text-nowrap">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceivable, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-1 d-none d-lg-flex justify-content-center">
                <div class="border-start opacity-25" style="height: 100%;"></div>
            </div>

            {{-- Party Breakdown --}}
            <div class="col-lg-6">
                <h6 class="text-uppercase fw-bold text-muted ls-1 mb-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">
                    <i class="fas fa-users-cog me-1"></i>Collector Performance Breakdown
                </h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0 align-middle">
                        <thead>
                            <tr class="border-bottom small text-muted">
                                <th class="ps-0 pb-2">COLLECTED BY (PARTY)</th>
                                <th class="text-end pe-0 pb-2">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($partySummary as $pSummary)
                            <tr>
                                <td class="ps-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-2">
                                            <i class="fas fa-user-check text-primary small"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $pSummary['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-0 py-2">
                                    <span class="badge bg-light text-dark border p-2 fw-bold text-nowrap">
                                        {{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($pSummary['amount'], 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 0.1rem; }
    .table thead th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #6c757d;
        border-bottom-width: 1px;
    }
    .table td {
        font-size: 0.85rem;
    }
    .card-header.bg-dark {
        background: #1a1a1a !important;
    }
</style>
@endsection

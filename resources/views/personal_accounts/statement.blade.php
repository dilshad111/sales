@extends('layouts.app')

@section('title', 'Financial Statement - ' . $user->name)

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --brand-blue: #6366f1;
        --brand-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface: #ffffff;
        --bg: #f8fafc;
        --text-base: #1e293b;
        --text-dim: #64748b;
        --border: #e2e8f0;
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--bg);
        color: var(--text-base);
    }

    .statement-header {
        background: var(--brand-gradient);
        border-radius: 24px;
        padding: 1.5rem 3rem;
        min-height: 30mm;
        display: flex;
        align-items: center;
        margin-bottom: 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .statement-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }

    .metric-card:hover { border-color: var(--brand-blue); transform: translateY(-3px); }

    .history-card {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: 0 10px 30px -15px rgba(0,0,0,0.05);
    }

    .table-history thead th {
        background: #fdfdff;
        padding: 1.5rem 1rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: var(--text-dim);
        border-bottom: 1px solid var(--border);
    }

    .table-history tbody td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
    }

    .tx-type-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        font-weight: 600;
    }

    .balance-indicator { font-weight: 700; font-size: 1.1rem; }

    .btn-action-group .btn {
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
    }
</style>

<div class="container py-5">
    <!-- Header -->
    <div class="statement-header">
        <div class="row align-items-center w-100">
            <div class="col-md-7">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('personal_accounts.index') }}" class="text-white opacity-75 text-decoration-none">Partners</a></li>
                        <li class="breadcrumb-item active text-white fw-600">Balance Sheet</li>
                    </ol>
                </nav>
                <h2 class="h2 fw-bold mb-1">{{ $user->name }}</h2>
                <p class="opacity-75 small fw-300 mb-0">Full record of commissions and payments.</p>
            </div>
            <div class="col-md-5 text-md-end d-flex justify-content-md-end gap-2">
                <a href="{{ route('personal_accounts.statement.pdf', $user) . '?' . http_build_query($filters) }}" class="btn btn-light shadow-sm">
                    <i class="far fa-file-pdf text-danger me-2"></i>Export PDF
                </a>
                <a href="{{ route('personal_accounts.index') }}" class="btn btn-white bg-opacity-10 text-white border border-white border-opacity-25 shadow-sm">
                    <i class="fas fa-chevron-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Period Indicator -->
    @if($filters['from_date'] || $filters['to_date'])
    <div class="alert bg-white border rounded-4 p-4 mb-4 d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-4 text-primary">
            <i class="fas fa-calendar-alt fa-lg"></i>
        </div>
        <div>
            <div class="small fw-700 text-uppercase text-muted ls-1">Date Range</div>
            <div class="h5 mb-0 fw-bold">
                {{ $filters['from_date'] ? \Carbon\Carbon::parse($filters['from_date'])->format('d M Y') : 'Start' }} 
                <span class="text-muted fw-400 mx-2">to</span> 
                {{ $filters['to_date'] ? \Carbon\Carbon::parse($filters['to_date'])->format('d M Y') : 'Today' }}
            </div>
        </div>
    </div>
    @endif

    <!-- Metrics Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="small fw-700 text-uppercase text-muted ls-1 mb-3">Total Commission</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($statement['commission_total'], 2) }}</div>
                <div class="small text-success mt-2"><i class="fas fa-wallet me-1"></i> Total Earned</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="small fw-700 text-uppercase text-muted ls-1 mb-3">Total Paid</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($statement['payment_total'], 2) }}</div>
                <div class="small text-primary mt-2"><i class="fas fa-check-circle me-1"></i> Payments Made</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="small fw-700 text-uppercase text-muted ls-1 mb-3">Payable Balance</div>
                <div class="h3 fw-bold {{ $statement['balance'] > 0.01 ? 'text-danger' : 'text-success' }} mb-0">
                    {{ number_format(abs($statement['balance']), 2) }}
                    <span class="small fw-600">{{ $statement['balance'] > 0.01 ? 'Dr.' : 'Cr.' }}</span>
                </div>
                <div class="small mt-2 opacity-75">Net Outstanding</div>
            </div>
        </div>
    </div>

    @php
        // Advanced Display grouping to prevent "Double Entry" confusion in the ledger view
        // We group entries by their base transaction to find the net effect
        $entries = collect($statement['entries']);
        $groupedEntries = $entries; // By default we keep them detailed for ledger transparency
        
        // However, if there are multiple entries for the same transaction_id on the SAME account
        // (which happens due to mirror/contra), we condense them for visual sanity
        // Actually, PersonalAccountController@buildStatement already retrieves entries for ONE account_id.
        // If one account has multiple entries in one transaction, we net them.
    @endphp

    <!-- History -->
    <div class="history-card">
        <div class="p-4 border-bottom bg-light bg-opacity-25 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">Transaction History</h5>
            <span class="badge bg-dark rounded-pill px-3">{{ $entries->count() }} Records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-history mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Type</th>
                        <th>Remarks / Ref</th>
                        <th class="text-end">Paid (-)</th>
                        <th class="text-end">Commission (+)</th>
                        <th class="text-end pe-4">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-700 text-dark">{{ \Carbon\Carbon::parse($entry['date'])->format('d M, Y') }}</div>
                            <div class="small text-muted">{{ \Carbon\Carbon::parse($entry['date'])->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="tx-type-badge {{ $entry['type'] === 'commission' ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary' }}">
                                {{ strtoupper($entry['type']) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-600 text-dark">{{ $entry['reference'] ?: 'Direct Entry' }}</div>
                            <div class="small text-muted">{{ $entry['notes'] ?: 'No additional narration provided' }}</div>
                        </td>
                        <td class="text-end">
                            @if($entry['payment_amount'] > 0)
                                <span class="fw-600 text-primary">-{{ number_format($entry['payment_amount'], 2) }}</span>
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($entry['commission_amount'] > 0)
                                <span class="fw-600 text-success">+{{ number_format($entry['commission_amount'], 2) }}</span>
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span class="balance-indicator {{ $entry['running_balance'] > 0.01 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($entry['running_balance']), 2) }}
                                <span class="small fw-600" style="font-size: 0.65rem;">{{ $entry['running_balance'] > 0.01 ? 'Dr.' : 'Cr.' }}</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No historical data available for this entity in the specified range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Voucher ' . $voucher->transaction_number)

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-glass: rgba(255, 255, 255, 0.9);
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-soft: #f1f5f9;
        --success-glow: 0 0 20px rgba(34, 197, 94, 0.15);
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #edf2f7;
        background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0, transparent 50%), 
                          radial-gradient(at 50% 0%, rgba(79, 70, 229, 0.05) 0, transparent 50%);
    }

    .voucher-card {
        border: none;
        border-radius: 24px;
        background: var(--surface-glass);
        backdrop-filter: blur(10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .voucher-header {
        background: var(--primary-gradient);
        padding: 1.5rem 2.5rem;
        position: relative;
    }

    .voucher-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(to top, var(--surface-glass), transparent);
    }

    .badge-voucher {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.7rem;
    }

    .info-card {
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 1.25rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
    }

    .table-voucher thead th {
        background: transparent;
        border-bottom: 2px solid var(--border-soft);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1.5rem 1rem;
        letter-spacing: 1px;
    }

    .table-voucher tbody tr {
        border-bottom: 1px solid var(--border-soft);
        transition: background-color 0.2s ease;
    }

    .table-voucher tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.02);
    }

    .amount-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .total-footer {
        background: #fdfdff;
        border-radius: 0 0 24px 24px;
        padding: 2.5rem 3rem;
    }

    .btn-action {
        border-radius: 12px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-print {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        color: white;
    }

    .internal-transfer-badge {
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #64748b;
        margin-top: 4px;
        display: inline-block;
    }

    .signature-box {
        border-top: 2px dashed #e2e8f0;
        padding-top: 1.5rem;
        margin-top: 4rem;
    }
</style>

<div class="container py-5">
    <!-- Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vouchers.index') }}" class="text-muted text-decoration-none">Vouchers</a></li>
                    <li class="breadcrumb-item active fw-600">{{ $voucher->transaction_number }}</li>
                </ol>
            </nav>
            <h1 class="h2 fw-bold text-gray-800 mb-0">
                @php
                    echo match($voucher->type) {
                        'PV' => 'Payment Voucher',
                        'RV' => 'Payment Receipt',
                        'JV' => 'Journal Voucher',
                        default => $voucher->formatted_type
                    };
                @endphp
            </h1>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('vouchers.print', $voucher) }}" target="_blank" class="btn btn-action btn-print shadow-none">
                <i class="fas fa-print me-2"></i>Print Voucher
            </a>
            <a href="{{ route('vouchers.index') }}" class="btn btn-action btn-light border shadow-none">
                <i class="fas fa-chevron-left me-2"></i>Back
            </a>
        </div>
    </div>

    @php
        // Condense entries by account to solve "Double Entry" visual confusion
        $condensedEntries = collect($voucher->entries)->groupBy('account_id')->map(function ($items) {
            $totalDebit = $items->sum('debit');
            $totalCredit = $items->sum('credit');
            $account = $items->first()->account;
            
            return (object)[
                'account' => $account,
                'debit' => $totalDebit > $totalCredit ? $totalDebit - $totalCredit : 0,
                'credit' => $totalCredit > $totalDebit ? $totalCredit - $totalDebit : 0,
                'is_adjusted' => $items->count() > 1
            ];
        })->filter(fn($e) => $e->debit > 0.01 || $e->credit > 0.01);
    @endphp

    <div class="voucher-card">
        <!-- Header Section -->
        <div class="voucher-header text-white">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-4">
                        <div class="bg-white bg-opacity-10 p-2 rounded-4 backdrop-blur">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <div>
                            <span class="badge-voucher mb-2">
                                @php
                                     echo match($voucher->type) {
                                         'PV' => 'Payment Voucher',
                                         'RV' => 'Receive Voucher',
                                         'JV' => 'Journal Voucher',
                                         default => $voucher->type
                                     };
                                @endphp
                            </span>
                            <h2 class="h1 fw-bold mb-0" style="font-size: 2rem;">{{ $voucher->transaction_number }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="d-inline-block text-start p-2 bg-white bg-opacity-10 rounded-4">
                        <div class="small text-uppercase opacity-75 fw-bold mb-1 tracking-widest" style="font-size: 0.65rem;">Transaction Status</div>
                        <div class="h5 mb-0 fw-bold d-flex align-items-center">
                            <span class="bg-success rounded-circle me-2" style="width: 10px; height: 10px; box-shadow: var(--success-glow)"></span>
                            Verified Post
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 p-md-4">
            <!-- Details Grid -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="small fw-600 text-uppercase text-muted mb-2 ls-1">Posting Date</div>
                        <div class="h5 fw-bold mb-0 text-dark">{{ $voucher->date->format('l, d M Y') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="small fw-600 text-uppercase text-muted mb-2 ls-1">Method</div>
                        <div class="h5 fw-bold mb-0 text-primary text-uppercase">{{ $voucher->payment_mode ?: 'Ledger Adjustment' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card bg-light border-0">
                        <div class="small fw-600 text-uppercase text-muted mb-2 ls-1">Narration</div>
                        <div class="h6 mb-0 text-dark fw-500 line-height-base">{{ $voucher->narration ?: 'Standard operational transaction entry with no additional remarks.' }}</div>
                    </div>
                </div>
            </div>

            <!-- Entries Table -->
            <div class="table-responsive">
                <table class="table table-voucher align-middle">
                    <thead>
                        <tr>
                            <th style="min-width: 400px;">Account Designation</th>
                            <th class="text-end" style="width: 200px;">Debit ({{ $companySetting->currency_symbol ?? 'PKR' }})</th>
                            <th class="text-end pe-4" style="width: 200px;">Credit ({{ $companySetting->currency_symbol ?? 'PKR' }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($condensedEntries as $entry)
                        <tr>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-3 rounded-3 me-3">
                                        <i class="fas {{ $entry->debit > 0 ? 'fa-arrow-up text-primary' : 'fa-arrow-down text-success' }}"></i>
                                    </div>
                                    <div>
                                        <div class="h6 mb-1 fw-bold text-dark">{{ $entry->account->name }}</div>
                                        <div class="small text-muted text-uppercase tracking-wider fw-500">{{ $entry->account->type }}</div>
                                        @if($entry->is_adjusted)
                                            <span class="internal-transfer-badge">
                                                <i class="fas fa-sync-alt me-1"></i> Special Partner Adjustment Applied
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end py-4">
                                @if($entry->debit > 0)
                                    <span class="amount-text text-dark">{{ number_format($entry->debit, 2) }}</span>
                                @else
                                    <span class="text-muted opacity-25">—</span>
                                @endif
                            </td>
                            <td class="text-end py-4 pe-4">
                                @if($entry->credit > 0)
                                    <span class="amount-text text-dark">{{ number_format($entry->credit, 2) }}</span>
                                @else
                                    <span class="text-muted opacity-25">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-4 px-md-5 pb-5">
            <!-- Totals Section -->
            <div class="row justify-content-end mb-4">
                <div class="col-md-5">
                    <div class="bg-light p-4 rounded-4 border-start border-primary border-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small fw-700 text-uppercase text-muted ls-1">Document Value</div>
                                <div class="h3 fw-bold mb-0 text-dark">GRAND TOTAL</div>
                            </div>
                            <div class="text-end">
                                <div class="h2 fw-bold text-primary mb-0">
                                    {{ $companySetting->currency_symbol ?? 'RS.' }} {{ number_format($voucher->total_amount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="row text-center g-4 mt-2">
                <div class="col-md-4 text-start">
                    <div class="p-3 border-top border-2 border-dark border-opacity-10">
                        <span class="small text-muted text-uppercase fw-700 ls-1">Prepared By</span>
                        <div class="h6 mt-2 fw-700 text-dark">{{ $voucher->created_by_user->name }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border-top border-2 border-dark border-opacity-10">
                        <span class="small text-muted text-uppercase fw-700 ls-1">Verified By</span>
                        <div class="h6 mt-2 text-muted opacity-25">___________________</div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="p-3 border-top border-2 border-dark border-opacity-10">
                        <span class="small text-muted text-uppercase fw-700 ls-1">Authorized Official</span>
                        <div class="h6 mt-2 text-muted opacity-25">___________________</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .fw-500 { font-weight: 500; }
    .fw-600 { font-weight: 600; }
    .fw-700 { font-weight: 700; }
    .backdrop-blur { backdrop-filter: blur(8px); }
</style>
@endsection

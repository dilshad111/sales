@extends('layouts.app')

@section('title', 'Network Financial Management')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --brand-blue: #6366f1;
        --brand-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface: #ffffff;
        --bg: #f8fafc;
        --text-base: #334155;
        --text-dim: #64748b;
        --border: #e2e8f0;
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--bg);
        color: var(--text-base);
    }

    .glass-header {
        background: var(--brand-gradient);
        border-radius: 20px;
        padding: 1.5rem 3rem;
        min-height: 30mm;
        display: flex;
        align-items: center;
        margin-bottom: 2.5rem;
        color: white;
        box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.4);
    }

    .stats-card {
        background: white;
        border: none;
        border-radius: 18px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
    }

    .search-panel {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid var(--border);
        margin-bottom: 2rem;
    }

    .premium-table {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .premium-table thead th {
        background: #fdfdff;
        color: var(--text-dim);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1.2px;
        padding: 1.5rem 1rem;
        border-bottom: 1px solid var(--border);
    }

    .premium-table tbody td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--brand-blue);
        font-size: 1.2rem;
    }

    .balance-pill {
        padding: 0.4rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }

    .btn-premium {
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-create {
        background: white;
        color: var(--brand-blue);
        border: none;
    }

    .form-control-premium {
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        border: 1px solid var(--border);
        background: #f8fafc;
    }

    .form-control-premium:focus {
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        border-color: var(--brand-blue);
    }
</style>

<div class="container py-5">
    <!-- Main Header -->
    <div class="glass-header d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1">Financial Partners</h2>
            <p class="opacity-75 mb-0 fw-500 small">Global oversight of individual liabilities and commissions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-premium btn-create shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#commissionModal">
                <i class="fas fa-plus-circle me-2"></i>Add Commission
            </button>
            <a href="{{ route('vouchers.create', ['type' => 'PV']) }}" class="btn btn-premium btn-dark shadow-sm px-4">
                <i class="fas fa-money-bill-wave me-2"></i>Make Payment
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="search-panel">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-700 text-uppercase text-muted ls-1">Name Or Identity</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 rounded-start-3" style="border-radius: 12px 0 0 12px; border: 1px solid var(--border);">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-premium border-start-0" 
                           style="border-radius: 0 12px 12px 0;"
                           value="{{ $filters['search'] ?? '' }}" placeholder="Filter by financial entity...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-700 text-uppercase text-muted ls-1">From</label>
                <input type="date" name="from_date" class="form-control form-control-premium" value="{{ $filters['from_date'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-700 text-uppercase text-muted ls-1">To</label>
                <input type="date" name="to_date" class="form-control form-control-premium" value="{{ $filters['to_date'] ?? '' }}">
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-premium btn-primary py-3">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="premium-table">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Entity Name</th>
                        <th class="text-end">Total Commission</th>
                        <th class="text-end">Total Paid</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $commissionTotal = (float) ($user->total_commission ?? 0);
                            $paymentTotal = (float) ($user->total_payments ?? 0);
                            $balance = (float) ($user->account_balance ?? 0);
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar text-uppercase">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-700 text-dark h6 mb-0">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email ?: 'Internal Partner' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end fw-500 text-dark">
                                {{ number_format($commissionTotal, 2) }}
                            </td>
                            <td class="text-end fw-500 text-dark">
                                {{ number_format($paymentTotal, 2) }}
                            </td>
                            <td class="text-end">
                                <div class="balance-pill {{ $balance > 0.01 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
                                    {{ $balance > 0.01 ? 'Dr.' : 'Cr.' }} {{ number_format(abs($balance), 2) }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                    <a href="{{ route('personal_accounts.statement', $user) }}" class="btn btn-sm btn-white border px-3" title="View Statement">
                                        <i class="far fa-file-alt text-primary"></i>
                                    </a>
                                    @if($user->account_id)
                                    <a href="{{ route('vouchers.create', ['type' => 'PV', 'account_id' => $user->account_id]) }}" class="btn btn-sm btn-white border px-3" title="Pay Now">
                                        <i class="fas fa-coins text-success"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-3">
                                    <i class="fas fa-folder-open fa-3x"></i>
                                </div>
                                <div class="h5 fw-bold text-muted">No financial partners located</div>
                                <p class="text-muted small">Try refining your search parameters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Modal Upgrade -->
<style>
    .modal-content { border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.2); }
    .modal-header { background: var(--brand-gradient); color: white; border: none; padding: 2rem; }
    .modal-footer { border: none; padding: 1.5rem 2rem; }
</style>

<div class="modal fade" id="commissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title fw-bold" id="commissionModalLabel">Add Commission</h4>
                    <p class="mb-0 small opacity-75">Record new commission for a partner</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('personal_accounts.commissions.store') }}">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                <div class="modal-body p-4 p-md-5">
                    <div class="mb-4">
                        <label class="form-label small fw-700 text-uppercase ls-1">Target Entity</label>
                        <select name="user_id" class="form-select form-control-premium" required>
                            <option value="">Select Partner...</option>
                            @foreach($usersForForms as $userOption)
                                <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-700 text-uppercase ls-1">Date</label>
                            <input type="date" name="commission_date" class="form-control form-control-premium" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-700 text-uppercase ls-1">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-premium" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-700 text-uppercase ls-1">Remarks / Notes</label>
                        <textarea name="notes" class="form-control form-control-premium" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-5">
                    <button type="button" class="btn btn-premium btn-light border" data-bs-dismiss="modal">Dismiss</button>
                    <button type="submit" class="btn btn-premium btn-primary px-4">Finalize Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

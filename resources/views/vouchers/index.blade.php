@extends('layouts.app')

@section('title', 'Vouchers')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Accounting Vouchers
        </h1>
        <div class="dropdown">
            <button class="btn btn-primary shadow-sm dropdown-toggle fw-bold" type="button" id="newVoucherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-plus mr-1"></i> New Voucher
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="newVoucherDropdown" style="border-radius: 12px;">
                <li><a class="dropdown-item py-2" href="{{ route('vouchers.create', ['type' => 'PV']) }}"><i class="fas fa-money-check-dollar mr-2 text-success"></i> Payment Voucher (PV)</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('vouchers.create', ['type' => 'RV']) }}"><i class="fas fa-receipt mr-2 text-warning"></i> Receive Voucher (RV)</a></li>
                <li><div class="dropdown-divider"></div></li>
                <li><a class="dropdown-item py-2" href="{{ route('vouchers.create', ['type' => 'JV']) }}"><i class="fas fa-file-signature mr-2 text-primary"></i> Journal Voucher (JV)</a></li>
            </ul>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form method="GET" action="{{ route('vouchers.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-muted text-uppercase">Voucher Type</label>
                    <select name="type" class="form-select border-0 bg-light rounded-pill px-3">
                        <option value="">All Types</option>
                        <option value="RV" {{ request('type') == 'RV' ? 'selected' : '' }}>Receive Voucher (RV)</option>
                        <option value="PV" {{ request('type') == 'PV' ? 'selected' : '' }}>Payment Voucher (PV)</option>
                        <option value="JV" {{ request('type') == 'JV' ? 'selected' : '' }}>Journal Voucher (JV)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted text-uppercase">Financial Year</label>
                    <select name="financial_year_id" class="form-select border-0 bg-light rounded-pill px-3">
                        <option value="">All Years</option>
                        @foreach($financialYears as $fy)
                            <option value="{{ $fy->id }}" {{ request('financial_year_id') == $fy->id ? 'selected' : '' }}>{{ $fy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted text-uppercase">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control border-0 bg-light rounded-pill px-3">
                </div>
                <div class="col-md-2">
                    <label class="form-label small font-weight-bold text-muted text-uppercase">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control border-0 bg-light rounded-pill px-3">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 shadow-sm w-100">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['type', 'start_date', 'end_date', 'financial_year_id']))
                        <a href="{{ route('vouchers.index') }}" class="btn btn-light rounded-pill ms-2" title="Clear Filters">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Voucher No</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Narration</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                        <tr>
                            <td class="ps-4">
                                <span class="font-weight-bold text-primary">{{ $voucher->transaction_number }}</span>
                            </td>
                            <td>{{ $voucher->date->format('d M, Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($voucher->type) {
                                        'PV' => 'bg-danger',
                                        'RV' => 'bg-success',
                                        'JV' => 'bg-primary',
                                        'bill_payment' => 'bg-info',
                                        'third_party_payment' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };
                                    $typeName = match($voucher->type) {
                                        'PV' => 'Payment Voucher',
                                        'RV' => 'Receive Voucher',
                                        'JV' => 'Journal Voucher',
                                        'bill_payment' => 'Bill Settlement',
                                        'third_party_payment' => 'Partner Payment',
                                        'direct_payment' => 'Direct Pmt',
                                        default => ucwords(str_replace('_', ' ', $voucher->type))
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3" style="font-size: 0.75rem;">{{ $typeName }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="{{ $voucher->narration }}">
                                    {{ $voucher->narration ?: '-' }}
                                </div>
                            </td>
                            <td class="text-end font-weight-bold">
                                {{ number_format($voucher->total_amount, 2) }}
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group btn-group-sm rounded-pill overflow-hidden shadow-sm">
                                    <a href="{{ route('vouchers.edit', $voucher) }}" class="btn btn-outline-warning border-0" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('vouchers.show', $voucher) }}" class="btn btn-outline-primary border-0" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vouchers.print', $voucher) }}" target="_blank" class="btn btn-outline-info border-0" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger border-0" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="fas fa-receipt fa-4x"></i>
                                </div>
                                <h5 class="text-muted">No vouchers found matching your criteria.</h5>
                                <p class="text-muted small">Try adjusting your filters or create a new voucher.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vouchers->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $vouchers->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .font-weight-bold { font-weight: 700 !important; }
    .table-hover tbody tr:hover { background-color: rgba(66, 133, 244, 0.05); }
    .badge { font-weight: 600; letter-spacing: 0.5px; }
    .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-danger:hover {
        background-color: transparent !important;
        opacity: 0.7;
    }
</style>
@endsection

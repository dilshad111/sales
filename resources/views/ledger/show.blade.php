@extends('layouts.app')

@section('title', 'Ledger Statement - ' . $account->name)

@section('content')
<div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeIn">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ledger.index') }}">Ledger</a></li>
                        <li class="breadcrumb-item active">Statement</li>
                    </ol>
                </nav>
                <h3 class="h4 mb-0 fw-bold text-gray-800 d-flex align-items-center">
                    <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                        <i class="fas fa-book-open text-primary fa-sm"></i>
                    </span>
                    Ledger: {{ $account->name }}
                </h3>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('ledger.pdf', array_merge(['account' => $account->id], request()->all(), ['print' => 1])) }}" target="_blank" class="btn btn-sm px-3 shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-print me-1"></i>Print
                </a>
                <a href="{{ route('ledger.pdf', array_merge(['account' => $account->id], request()->all())) }}" class="btn btn-sm px-3 shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-file-pdf me-1"></i>Download PDF
                </a>
                <a href="{{ route('ledger.index') }}" class="btn btn-light btn-sm px-3 shadow-sm border" style="border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>
    <div class="card-body">
        <div class="row g-3 mb-4 bg-light p-3 rounded border">
            <div class="col-md-3"><strong>Account Type:</strong> {{ ucfirst($account->type) }}</div>
            <div class="col-md-3"><strong>Phone:</strong> {{ $account->phone ?? '-' }}</div>
            <div class="col-md-3"><strong>Period:</strong> {{ $startDate ? $startDate->format('d/m/Y') : 'Beginning' }} to {{ $endDate->format('d/m/Y') }}</div>
            <div class="col-md-3 text-end">
                <strong>Status:</strong> 
                <span class="badge bg-label-{{ $account->status == 'active' ? 'success' : 'danger' }} rounded-pill px-3">{{ ucfirst($account->status) }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light text-center small text-uppercase">
                    <tr>
                        <th>Date</th>
                        <th>TXN #</th>
                        <th>Type / Description</th>
                        <th>Related Account</th>
                        <th class="text-end">Debit ({{ $companySetting->currency_symbol ?? 'Rs.' }})</th>
                        <th class="text-end">Credit ({{ $companySetting->currency_symbol ?? 'Rs.' }})</th>
                        <th class="text-end">Balance ({{ $companySetting->currency_symbol ?? 'Rs.' }})</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr>
                        <td class="text-center">{{ $startDate ? $startDate->format('d/m/Y') : $account->created_at->format('d/m/Y') }}</td>
                        <td class="text-center fw-bold">OP-BAL</td>
                        <td colspan="2" class="fw-bold">Opening Balance</td>
                        <td class="text-end"></td>
                        <td class="text-end"></td>
                        <td class="text-end fw-bold {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($openingBalance, 2) }}
                        </td>
                    </tr>

                    @php $runningBalance = $openingBalance; @endphp
                    @forelse($entries as $entry)
                        @php $runningBalance += ($entry->debit - $entry->credit); @endphp
                        <tr class="text-center">
                            <td>{{ $entry->transaction->date->format('d/m/Y') }}</td>
                            <td class="small fw-bold">
                                @if(in_array($entry->transaction->type, ['PV', 'RV', 'JV', 'CPV', 'BPV', 'third_party_payment', 'multi_party_adjustment']))
                                    <a href="{{ route('vouchers.show', $entry->transaction_id) }}" class="text-primary">{{ $entry->transaction->transaction_number }}</a>
                                @else
                                    {{ $entry->transaction->transaction_number }}
                                @endif

                            </td>
                            <td class="text-start">
                                <div class="small fw-bold">
                                    {{ $entry->transaction->formatted_type }}
                                </div>
                                <div class="small text-muted font-italic">{{ $entry->transaction->narration }}</div>
                            </td>
                            <td>
                                <!-- Show the OTHER account in this transaction -->
                                @php 
                                    $otherEntry = $entry->transaction->entries->where('account_id', '!=', $account->id)->first();
                                @endphp
                                <span class="small font-weight-bold">{{ $otherEntry->account->name ?? '-' }}</span>
                            </td>
                            <td class="text-end text-success fw-bold">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '' }}</td>
                            <td class="text-end text-danger fw-bold">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '' }}</td>
                            <td class="text-end fw-bold {{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No transactions recorded in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light border-top border-2">
                    <tr class="text-end fw-bold h6">
                        <td colspan="4" class="text-center">SUMMARY</td>
                        <td class="text-success">{{ number_format($entries->sum('debit'), 2) }}</td>
                        <td class="text-danger">{{ number_format($entries->sum('credit'), 2) }}</td>
                        <td class="{{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($runningBalance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

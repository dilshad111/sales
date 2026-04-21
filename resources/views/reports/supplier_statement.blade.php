@extends('layouts.app')

@section('title', 'Supplier Statement')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="h4 mb-0 fw-bold text-gray-800 d-flex align-items-center">
                <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                    <i class="fas fa-file-lines text-primary fa-sm"></i>
                </span>
                Supplier Statement
            </h3>
        </div>
    </div>
</div>

<form method="GET" class="mb-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="supplier_id" class="form-label fw-bold small">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-select select2" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-bold small">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-bold small">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Generate</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if($supplier)
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-truck-loading me-2 text-primary"></i>Supplier Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 border-end">
                <div class="mb-2"><span class="text-muted small text-uppercase">Name:</span> <span class="fw-bold ms-2">{{ $supplier->name }}</span></div>
                <div class="mb-2"><span class="text-muted small text-uppercase">Phone:</span> <span class="fw-bold ms-2">{{ $supplier->phone ?: 'N/A' }}</span></div>
            </div>
            <div class="col-md-6 ps-md-4">
                <div class="mb-2"><span class="text-muted small text-uppercase">Address:</span> <span class="fw-bold ms-2">{{ $supplier->address ?: 'N/A' }}</span></div>
                @if($startDate || $endDate)
                <div class="mb-0"><span class="text-muted small text-uppercase">Period:</span> 
                    <span class="fw-bold ms-2">
                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Beginning' }} to {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Today' }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-success"></i>Statement Details</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase fw-bold text-muted">
                        <th class="px-4 py-3">Date</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Ref #</th>
                        <th class="py-3" style="width: 40%;">Description</th>
                        <th class="py-3 text-end">Debit (-)</th>
                        <th class="py-3 text-end">Credit (+)</th>
                        <th class="px-4 py-3 text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr class="{{ $tx['type'] == 'opening_balance' ? 'bg-light fw-bold' : '' }}">
                        <td class="px-4">{{ \Carbon\Carbon::parse($tx['date'])->format('d/m/Y') }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $tx['type'])) }}</td>
                        <td>{{ $tx['reference'] }}</td>
                        <td class="small">{{ $tx['description'] }}</td>
                        <td class="text-end text-danger">{{ $tx['debit'] > 0 ? number_format($tx['debit'], 2) : '-' }}</td>
                        <td class="text-end text-success">{{ $tx['credit'] > 0 ? number_format($tx['credit'], 2) : '-' }}</td>
                        <td class="px-4 text-end fw-bold {{ $tx['balance'] < 0 ? 'text-primary' : ($tx['balance'] > 0 ? 'text-danger' : '') }}">
                            {{ number_format(abs($tx['balance']), 2) }} {{ $tx['balance'] < 0 ? 'CR' : ($tx['balance'] > 0 ? 'DR' : '') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-end">Closing Balance:</td>
                        <td class="text-end py-3">{{ number_format($transactions->filter(fn($t) => $t['type'] != 'opening_balance')->sum('debit'), 2) }}</td>
                        <td class="text-end py-3 text-success">{{ number_format($transactions->filter(fn($t) => $t['type'] != 'opening_balance')->sum('credit'), 2) }}</td>
                        <td class="px-4 py-3 text-end text-primary">
                            {{ number_format(abs($transactions->last()['balance']), 2) }} {{ $transactions->last()['balance'] < 0 ? 'CR' : ($transactions->last()['balance'] > 0 ? 'DR' : '') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@else
<div class="card shadow-sm border-0">
    <div class="card-body py-5 text-center">
        <div class="text-muted">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <p class="mb-0">Please select a supplier and date range to generate the statement.</p>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });
    });
</script>
@endpush
@endsection

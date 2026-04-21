@extends('layouts.app')

@section('title', 'Principal Payout Report')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Reports /</span> Principal Payout Report
</h4>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100">Filter Payouts</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    @foreach($summary as $stats)
    <div class="col-md-4">
        <div class="card bg-label-success border-0 shadow-none">
            <div class="card-body text-center">
                <div class="text-muted small mb-1">{{ $stats['account']->name }}</div>
                <div class="fs-3 fw-bold text-success">Rs. {{ number_format($stats['total'], 2) }}</div>
                <div class="small">Total Net Profit Transferred</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0">
    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead class="bg-light">
                <tr>
                    <th>Date</th>
                    <th>Recovery Ref</th>
                    <th>Purchase Inv</th>
                    <th>Partner Account</th>
                    <th class="text-end">Recovery</th>
                    <th class="text-end">Comm (-)</th>
                    <th class="text-end">Net Payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recoveries as $recovery)
                <tr>
                    <td>{{ $recovery->date->format('d/m/Y') }}</td>
                    <td>{{ $recovery->recovery_number }}</td>
                    <td>{{ $recovery->invoice->invoice_number }}</td>
                    <td><span class="badge bg-label-primary">{{ $recovery->directorAccount->name }}</span></td>
                    <td class="text-end text-muted">{{ number_format($recovery->amount, 2) }}</td>
                    <td class="text-end text-danger">-{{ number_format($recovery->commission_deducted, 2) }}</td>
                    <td class="text-end fw-bold text-success">{{ number_format($recovery->net_amount_transfered, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold fs-5">
                    <td colspan="6" class="text-end">GRAND TOTAL PAYOUT:</td>
                    <td class="text-end text-success">Rs. {{ number_format($recoveries->sum('net_amount_transfered'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

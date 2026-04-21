@extends('layouts.app')

@section('title', 'Agent Commission Report')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Reports /</span> Agent Commission Report
</h4>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3 text-start">
                <div class="col-md-4 text-start">
                    <label class="form-label">Filter Agent</label>
                    <select name="agent_id" class="form-select select2">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-info w-100">Show Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    @foreach($data as $agentId => $stats)
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-start border-primary border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 fw-bold">{{ $stats['agent']->name ?? 'Unknown Agent' }}</h5>
                    <span class="badge bg-label-primary">{{ $stats['invoice_count'] }} Invoices</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block uppercase" style="font-size: 10px;">TOTAL PURCHASE VOLUME</small>
                    <div class="fs-5 fw-bold">Rs. {{ number_format($stats['total_purchases'], 2) }}</div>
                </div>
                <div class="pt-2 border-top">
                    <small class="text-muted d-block uppercase" style="font-size: 10px;">TOTAL COMMISSION EARNED</small>
                    <div class="fs-4 fw-bold text-success">Rs. {{ number_format($stats['total_commission'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($data->isEmpty())
<div class="card py-5">
    <div class="text-center text-muted">No commission data found for this period.</div>
</div>
@endif

@endsection

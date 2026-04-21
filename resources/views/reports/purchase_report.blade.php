@extends('layouts.app')

@section('title', 'Purchase Report')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Reports /</span> Purchase Report
</h4>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select select2">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Agent</label>
                    <select name="agent_id" class="form-select select2">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead class="bg-light">
                <tr>
                    <th>Date</th>
                    <th>Inv #</th>
                    <th>Supplier</th>
                    <th>Agent</th>
                    <th class="text-end">Gross</th>
                    <th class="text-end">Commission</th>
                    <th class="text-end">Net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->date->format('d/m/Y') }}</td>
                    <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->supplier->name }}</td>
                    <td>{{ $invoice->agent->name ?? 'Direct' }}</td>
                    <td class="text-end">{{ number_format($invoice->gross_amount, 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($invoice->commission_amount, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($invoice->net_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-label-secondary">
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">TOTALS:</td>
                    <td class="text-end text-black">{{ number_format($invoices->sum('gross_amount'), 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($invoices->sum('commission_amount'), 2) }}</td>
                    <td class="text-end text-primary">{{ number_format($invoices->sum('net_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

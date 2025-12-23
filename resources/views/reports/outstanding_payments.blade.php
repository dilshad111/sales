@extends('layouts.app')

@section('title', 'Outstanding Payments Report')

@section('content')
<h1>Outstanding Payments Report</h1>

<a href="{{ route('reports.outstanding_payments_pdf', request()->query()) }}" class="btn btn-primary mb-3">Download PDF</a>

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <select name="customer_id" class="form-control">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="outstanding" {{ request('status') == 'outstanding' ? 'selected' : '' }}>Outstanding</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </div>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Bill #</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Outstanding</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bills as $data)
        <tr>
            <td>{{ $data['bill']->bill_number }}</td>
            <td>{{ $data['bill']->customer->name }}</td>
            <td>{{ $data['bill']->bill_date->format('d/m/Y') }}</td>
            <td>{{ number_format($data['bill']->total, 2) }}</td>
            <td>{{ number_format($data['paid'], 2) }}</td>
            <td>{{ number_format($data['outstanding'], 2) }}</td>
            <td>
                <span class="badge bg-{{ $data['status'] == 'paid' ? 'success' : ($data['status'] == 'partially_paid' ? 'warning' : 'danger') }}">
                    {{ ucfirst(str_replace('_', ' ', $data['status'])) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    <h4>Summary</h4>
    <p><strong>Total Billed:</strong> {{ number_format($summary['total_billed'], 2) }}</p>
    <p><strong>Total Paid:</strong> {{ number_format($summary['total_paid'], 2) }}</p>
    <p><strong>Total Outstanding:</strong> {{ number_format($summary['total_outstanding'], 2) }}</p>
</div>
@endsection

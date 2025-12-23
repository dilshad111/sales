@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<h1><i class="fas fa-chart-line me-2"></i>Sales Report</h1>

<a href="{{ route('reports.sales_pdf', request()->query()) }}" class="btn btn-primary mb-3"><i class="fas fa-download me-1"></i>Download PDF</a>

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <label for="customer_id" class="form-label"><i class="fas fa-users me-1"></i>Customer</label>
            <select name="customer_id" class="form-control">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="start_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2">
            <label for="end_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-4">
        <div class="card dashboard-card border-success">
            <div class="card-body">
                <h5 class="card-title text-success"><i class="fas fa-chart-line me-2"></i>Total Sales</h5>
                <p class="card-text h4">₨{{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card border-info">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="fas fa-money-bill-wave me-2"></i>Total Payments Received</h5>
                <p class="card-text h4">₨{{ number_format($totalPayments, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Total Outstanding</h5>
                <p class="card-text h4">₨{{ number_format($totalOutstanding, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<h3 class="mt-4"><i class="fas fa-box me-2"></i>Item-wise Sales</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th><i class="fas fa-box me-1"></i>Item</th>
            <th><i class="fas fa-hashtag me-1"></i>Quantity Sold</th>
            <th><i class="fas fa-rupee-sign me-1"></i>Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($itemSales as $itemData)
        <tr>
            <td>{{ $itemData['item']->name }}</td>
            <td>{{ $itemData['quantity'] }}</td>
            <td>₨{{ number_format($itemData['total'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3 class="mt-4"><i class="fas fa-file-invoice me-2"></i>Bill Details</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th><i class="fas fa-hashtag me-1"></i>Bill #</th>
            <th><i class="fas fa-user me-1"></i>Customer</th>
            <th><i class="fas fa-calendar me-1"></i>Date</th>
            <th><i class="fas fa-rupee-sign me-1"></i>Total</th>
            <th><i class="fas fa-check me-1"></i>Paid</th>
            <th><i class="fas fa-exclamation-circle me-1"></i>Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bills as $bill)
        <tr>
            <td>{{ $bill->bill_number }}</td>
            <td>{{ $bill->customer->name }}</td>
            <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
            <td>₨{{ number_format($bill->total, 2) }}</td>
            <td>₨{{ number_format($bill->payments->sum('amount'), 2) }}</td>
            <td>₨{{ number_format($bill->total - $bill->payments->sum('amount'), 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

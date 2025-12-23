@extends('layouts.app')

@section('title', 'Customer Statement')

@section('content')
<h1><i class="fas fa-file-invoice-dollar me-2"></i>Customer Statement</h1>

@if($customer)
<a href="{{ route('reports.customer_statement_pdf', request()->query()) }}" class="btn btn-primary mb-3"><i class="fas fa-download me-1"></i>Download PDF</a>
@endif

<form method="GET" class="mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Options</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="customer_id" class="form-label"><i class="fas fa-users me-1"></i>Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search me-1"></i>Generate</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if($customer)
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong><i class="fas fa-user me-2"></i>Name:</strong> {{ $customer->name }}</p>
                <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $customer->phone }}</p>
            </div>
            <div class="col-md-6">
                <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> {{ $customer->email }}</p>
                <p><strong><i class="fas fa-map-marker-alt me-2"></i>Address:</strong> {{ $customer->address }}</p>
            </div>
        </div>
        @if($startDate || $endDate)
        <div class="mt-2">
            <p class="mb-0"><strong><i class="fas fa-calendar me-2"></i>Period:</strong> 
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @elseif($startDate)
                    From {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                @elseif($endDate)
                    Up to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Transaction Details</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center align-middle" style="width: 4%;"><i class="fas fa-hashtag me-1"></i>S. No.</th>
                        <th class="text-center align-middle" style="width: 7%;"><i class="fas fa-calendar me-1"></i>Date</th>
                        <th class="text-center align-middle" style="width: 8%;"><i class="fas fa-file-invoice me-1"></i>Bill No.</th>
                        <th class="text-center align-middle" style="width: 30%;"><i class="fas fa-box me-1"></i>Item Description</th>
                        <th class="text-center align-middle" style="width: 10%;"><i class="fas fa-cubes me-1"></i>Quantity</th>
                        <th class="text-center align-middle" style="width: 8%;"><i class="fas fa-rupee-sign me-1"></i>Rate/Each</th>
                        <th class="text-center align-middle" style="width: 11%;"><i class="fas fa-chart-line me-1"></i>Sales Amount</th>
                        <th class="text-center align-middle" style="width: 11%;"><i class="fas fa-money-bill-wave me-1"></i>Payment Received</th>
                        <th class="text-center align-middle" style="width: 11%;"><i class="fas fa-balance-scale me-1"></i>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $transaction)
                    <tr class="{{ $transaction['type'] == 'payment' ? 'table-success' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $transaction['bill_no'] }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td class="text-center">
                            @if($transaction['quantity'] !== '-')
                                {{ number_format($transaction['quantity'], 0) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($transaction['rate'] !== '-')
                                ₨{{ number_format($transaction['rate'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($transaction['sales_amount'] > 0)
                                ₨{{ number_format($transaction['sales_amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($transaction['payment_received'] > 0)
                                <span class="text-success fw-bold">₨{{ number_format($transaction['payment_received'], 2) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $transaction['balance'] > 0 ? 'text-danger' : ($transaction['balance'] < 0 ? 'text-success' : '') }}">
                            ₨{{ number_format($transaction['balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No transactions found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="6" class="text-end">Total:</th>
                        <th class="text-end">₨{{ number_format($transactions->sum('sales_amount'), 2) }}</th>
                        <th class="text-end text-success">₨{{ number_format($transactions->sum('payment_received'), 2) }}</th>
                        <th class="text-end fw-bold {{ $transactions->last()['balance'] > 0 ? 'text-danger' : ($transactions->last()['balance'] < 0 ? 'text-success' : '') }}">
                            ₨{{ number_format($transactions->last()['balance'], 2) }}
                        </th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@if($transactions->count() > 0)
<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Note:</strong> 
    @if($transactions->last()['balance'] > 0)
        Outstanding balance of <strong>₨{{ number_format($transactions->last()['balance'], 2) }}</strong> is due from the customer.
    @elseif($transactions->last()['balance'] < 0)
        Customer has a credit balance of <strong>₨{{ number_format(abs($transactions->last()['balance']), 2) }}</strong>.
    @else
        Account is fully settled.
    @endif
</div>
@endif

@else
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>Please select a customer and date range to view the statement.
</div>
@endif

@endsection

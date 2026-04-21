@extends('layouts.app')

@section('title', 'Sale Invoice Details')

@push('styles')
<style>
@media print {
    body {
        font-size: 12px;
    }
    .btn, .mt-3, .ms-2 {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    h1, h3 {
        color: black !important;
    }
    .table {
        font-size: 11px;
    }
}
</style>
@endpush

@section('content')
@php
    $billItems = $bill->billItems;
    $totalQuantity = $billItems->sum('quantity');
    $totalAmount = $billItems->sum('total');
@endphp

<h1 class="mb-4">Sale Invoice Details</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">Invoice Information</h5>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-3 mb-3">
            <div>
                <span class="text-muted d-block">Invoice #</span>
                <code class="fs-4">{{ $bill->bill_number }}</code>
            </div>
            <div class="text-end">
                <span class="text-muted d-block">Date</span>
                <strong>{{ $bill->bill_date->format('d/m/Y') }}</strong>
            </div>
        </div>
        <div>
            <p class="mb-1"><strong>Customer:</strong> {{ optional($bill->customer)->name ?? 'Customer Deleted' }}</p>
            <p class="mb-0 text-muted">{{ optional($bill->customer)->address ?? 'Address unavailable' }}</p>
        </div>
    </div>
</div>

<div class="card shadow-none border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr class="text-center">
                    <th style="width: 50px;">S.No</th>
                    <th class="text-start">Item Name</th>
                    <th>Delivery Date</th>
                    <th>Quantity</th>
                    <th>Price/each</th>
                    <th class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billItems as $billItem)
                <tr>
                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ optional($billItem->item)->name ?? 'Item Deleted' }}</div>
                        @if(!empty($billItem->remarks))
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;"><i class="fas fa-info-circle me-1"></i>{{ $billItem->remarks }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $billItem->delivery_date ? \Carbon\Carbon::parse($billItem->delivery_date)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center fw-semibold text-dark">{{ number_format($billItem->quantity) }}</td>
                    <td class="text-end fw-semibold">{{ number_format($billItem->price, 2) }}</td>
                    <td class="text-end fw-bold text-dark">{{ number_format($billItem->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light bg-opacity-50">
                <tr class="fw-bold">
                    <td colspan="3" class="text-end text-muted small uppercase">Subtotal</td>
                    <td class="text-center text-dark">{{ number_format($totalQuantity) }}</td>
                    <td></td>
                    <td class="text-end text-primary">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($bill->billExpenses->count() > 0)
<div class="mt-4">
    <h5 class="mb-3"><i class="fas fa-receipt me-2 text-warning"></i>Additional Expenses</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle" style="max-width: 600px;">
            <thead class="table-light">
                <tr>
                    <th>Expense Details</th>
                    <th class="text-end" style="width:200px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->billExpenses as $expense)
                <tr>
                    <td>{{ $expense->description }}</td>
                    <td class="text-end fw-semibold">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td class="text-end">Total Expenses</td>
                    <td class="text-end">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->billExpenses->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

</div>

<div class="row align-items-center mt-3">
    <div class="col-lg-7">
        <div class="p-3 bg-light rounded-3 border-start border-primary border-4 shadow-sm">
            <h6 class="text-uppercase small fw-bold text-muted mb-2"><i class="fas fa-file-invoice me-1"></i> Amount in Words (PKR)</h6>
            <div class="fw-bold text-dark font-italic" style="font-family: 'Outfit', sans-serif;">
                {{ $bill->amount_in_words() }}
            </div>
        </div>
    </div>
    <div class="col-lg-5 d-flex justify-content-end">
        <table class="table table-sm" style="max-width: 350px;">
            <tbody>
            <tr>
                <th>Subtotal</th>
                <td class="text-end fw-bold">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalAmount, 2) }}</td>
            </tr>
            @if($bill->discount > 0)
            <tr>
                <th>Discount</th>
                <td class="text-end text-danger">- {{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->discount, 2) }}</td>
            </tr>
            @endif
            @if($bill->tax > 0)
            <tr>
                <th>Tax {{ $bill->tax_percent > 0 ? '(' . number_format($bill->tax_percent, 2) . '%)' : '' }}</th>
                <td class="text-end">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->tax, 2) }}</td>
            </tr>
            @endif
            @if($bill->billExpenses->sum('amount') > 0)
            <tr>
                <th>Expenses</th>
                <td class="text-end">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->billExpenses->sum('amount'), 2) }}</td>
            </tr>
            @endif
            <tr class="table-light fw-bold fs-6">
                <th>Grand Total</th>
                <td class="text-end fw-bold text-primary">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="d-flex flex-wrap gap-2 mt-4">
    <a href="{{ route('bills.pdf', $bill) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;"><i class="fas fa-file-pdf me-1"></i>Download PDF</a>
    <a href="{{ route('bills.print', $bill) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;" target="_blank"><i class="fas fa-print me-1"></i>Print Invoice</a>
    <a href="{{ route('bills.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <form action="{{ route('bills.destroy', $bill) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this bill?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete Invoice</button>
    </form>
</div>
@endsection

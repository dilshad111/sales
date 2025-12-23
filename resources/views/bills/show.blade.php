@extends('layouts.app')

@section('title', 'Bill Details')

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

<h1 class="mb-4">Bill Details</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">Bill Information</h5>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-3 mb-3">
            <div>
                <span class="text-muted d-block">Bill #</span>
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

<h3 class="mt-5 mb-3">Bill Detail</h3>
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr class="text-center fw-semibold">
                <th style="width: 40px;">S.No</th>
                <th style="width: 360px;">Item Name</th>
                <th style="width: 100px;">Delivery Date</th>
                <th style="width: 90px;">Quantity</th>
                <th style="width: 60px;">Price/each</th>
                <th style="width: 140px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billItems as $billItem)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    <div>{{ optional($billItem->item)->name ?? 'Item Deleted' }}</div>
                    @if(!empty($billItem->remarks))
                        <div class="text-muted small mt-1">{{ $billItem->remarks }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $billItem->delivery_date ? \Carbon\Carbon::parse($billItem->delivery_date)->format('d/m/Y') : '-' }}</td>
                <td class="text-center fw-semibold">{{ number_format($billItem->quantity) }}</td>
                <td class="text-end fw-semibold">{{ number_format($billItem->price, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($billItem->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light fw-semibold text-center">
                <td colspan="3" class="text-end">Subtotal</td>
                <td>{{ number_format($totalQuantity) }}</td>
                <td>—</td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <a href="{{ route('bills.pdf', $bill) }}" class="btn btn-primary"><i class="fas fa-download me-1"></i>Download PDF</a>
    <a href="{{ route('bills.print', $bill) }}" class="btn btn-info" target="_blank"><i class="fas fa-print me-1"></i>Print Bill</a>
    <a href="{{ route('bills.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <form action="{{ route('bills.destroy', $bill) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this bill?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete Bill</button>
    </form>
</div>
@endsection

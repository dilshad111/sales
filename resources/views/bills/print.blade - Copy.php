@extends('layouts.app')

@section('title', 'Print Bill')

@push('styles')
<style>
@page {
    size: A4;
    margin-left: 3mm;
    margin-right: 3mm;
    margin-top: 1mm;
    margin-bottom: 1mm;
}
@media print {
    body {
        font-size: 12px;
        margin: 0;
    }
    .navbar,
    header,
    aside,
    nav,
    .sidebar,
    .app-footer {
        display: none !important;
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
body {
    font-size: 14px;
    margin: 0;
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
    font-size: 12px;
}
</style>
@endpush

@section('content')
@php
    $billItems = $bill->billItems;
    $totalQuantity = $billItems->sum('quantity');
    $totalAmount = $billItems->sum('total');
@endphp

<div class="container-fluid">
    <h1 class="text-center mb-1">{{ $companySetting->name }}</h1>
    <p class="text-center text-muted mb-2">{{ $companySetting->address }}</p>
    <hr class="my-2">
    <h3 class="text-center mb-4">Sales Bill</h3>

    <div class="mb-3">
        <table class="w-100" style="border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    <strong>Bill #:</strong> {{ $bill->bill_number }}
                </td>
                <td style="border: none; padding: 0;" class="text-end">
                    <strong>Date:</strong> {{ $bill->bill_date->format('d/m/Y') }}
                </td>
            </tr>
        </table>
        <p class="mb-0"><strong>Customer:</strong> {{ optional($bill->customer)->name ?? 'Customer Deleted' }}</p>
        <p class="text-muted">{{ optional($bill->customer)->address ?? 'Address unavailable' }}</p>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr class="text-center fw-semibold">
                <th style="width: 17px;">S.No</th>
                <th style="width: 275px;">Item Name</th>
                <th style="width: 70px;">Delivery Date</th>
                <th style="width: 50px;">Quantity</th>
                <th style="width: 24px;">Price/each</th>
                <th style="width: 80px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billItems as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    <div>{{ optional($item->item)->name ?? 'Item Deleted' }}</div>
                    @if(!empty($item->remarks))
                        <div style="margin-top: 3px; font-size: 14px; color: #000000ff;"> {{ $item->remarks }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : '-' }}</td>
                <td class="text-end fw-semibold">{{ number_format($item->quantity) }}</td>
                <td class="text-end fw-semibold">{{ number_format($item->price, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($item->total, 2) }}</td>
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

    <div class="text-center mt-4">
        <p class="small">This is a system-generated invoice and does not require a signature.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.navbar, header, nav, aside, .sidebar, .app-footer').forEach((el) => {
        if (el) {
            el.style.display = 'none';
        }
    });
});

window.onload = function() {
    window.print();
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'Purchase Invoices')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Purchases /</span> Invoices
    </h4>
    <a href="{{ route('purchase_invoices.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> New Purchase Invoice
    </a>
</div>

<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Dates</th>
                    <th>Inv #</th>
                    <th>Supplier Inv #</th>
                    <th>Supplier</th>
                    <th>Agent</th>
                    <th class="text-end">Gross</th>
                    <th class="text-end">Tax %</th>
                    <th class="text-end">Payable</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="ps-4">
                        <div class="small fw-bold text-primary">Post: {{ $invoice->posting_date->format('d/m/Y') }}</div>
                        <div class="small text-muted">Inv: {{ $invoice->date->format('d/m/Y') }}</div>
                    </td>
                    <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $invoice->supplier_invoice_number ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="fw-bold">{{ $invoice->supplier->name }}</span>
                    </td>
                    <td>
                        {{ $invoice->agent->name ?? 'Direct' }}
                    </td>
                    <td class="text-end text-muted">
                        {{ number_format($invoice->gross_amount, 2) }}
                    </td>
                    <td class="text-end text-danger">
                        {{ $invoice->tax_percentage }}%
                    </td>
                    <td class="text-end fw-bold text-primary">
                        {{ number_format($invoice->net_amount, 2) }}
                    </td>
                    <td class="text-center">
                        @php
                            $statusClass = [
                                'pending' => 'warning',
                                'partially_recovered' => 'info',
                                'recovered' => 'success'
                            ][$invoice->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-label-{{ $statusClass }} rounded-pill text-uppercase" style="font-size: 10px;">
                            {{ str_replace('_', ' ', $invoice->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('purchase_invoices.show', $invoice) }}" class="btn btn-icon btn-sm btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('purchase_invoices.destroy', $invoice) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">No purchase invoices found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $invoices->links() !!}
        </div>
    </div>
    @endif
</div>
@endsection

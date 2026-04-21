@extends('layouts.app')

@section('title', 'Delivery Challan ' . $deliveryChallan->challan_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-truck me-2 text-primary"></i>{{ $deliveryChallan->challan_number }}
        @if($deliveryChallan->status === 'pending')
            <span class="badge bg-label-warning ms-2">Pending</span>
        @else
            <span class="badge bg-label-success ms-2">Billed</span>
        @endif
    </h1>
    <div class="btn-group">
        <a href="{{ route('delivery_challans.print', $deliveryChallan) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="fas fa-print me-1"></i>Print
        </a>
        @if($deliveryChallan->status === 'pending')
        <a href="{{ route('delivery_challans.edit', $deliveryChallan) }}" class="btn btn-outline-warning">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        @endif
        <a href="{{ route('delivery_challans.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3"><i class="fas fa-info-circle me-1"></i>Challan Details</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Challan Number</th>
                        <td class="fw-bold">{{ $deliveryChallan->challan_number }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Challan Date</th>
                        <td>{{ $deliveryChallan->challan_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @if($deliveryChallan->status === 'pending')
                                <span class="badge bg-label-warning">Pending</span>
                            @else
                                <span class="badge bg-label-success">Billed</span>
                                @if($deliveryChallan->bill)
                                    <a href="{{ route('bills.show', $deliveryChallan->bill) }}" class="ms-1 small">
                                        → {{ $deliveryChallan->bill->bill_number }}
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @if($deliveryChallan->vehicle_number)
                    <tr>
                        <th class="text-muted">Vehicle Number</th>
                        <td>{{ $deliveryChallan->vehicle_number }}</td>
                    </tr>
                    @endif
                    @if($deliveryChallan->remarks)
                    <tr>
                        <th class="text-muted">General Remarks</th>
                        <td>{{ $deliveryChallan->remarks }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3"><i class="fas fa-user me-1"></i>Customer Details</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Customer</th>
                        <td class="fw-bold">{{ optional($deliveryChallan->customer)->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Address</th>
                        <td>{{ optional($deliveryChallan->customer)->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Phone</th>
                        <td>{{ optional($deliveryChallan->customer)->phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 text-uppercase small text-muted" style="width: 40px;">#</th>
                        <th class="text-center text-uppercase small text-muted" style="width: 140px;">Bundles</th>
                        <th class="text-uppercase small text-muted">Item Description</th>
                        <th class="text-center text-uppercase small text-muted" style="width: 120px;">Qty.</th>
                        <th class="ps-3 text-uppercase small text-muted">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php $lineNo = 1; @endphp
                    @foreach($deliveryChallan->items as $item)
                    <tr>
                        <td class="ps-3 text-muted">{{ $lineNo++ }}</td>
                        <td class="text-center fw-bold text-primary">{{ $item->bundles ?? '-' }}</td>
                        <td class="fw-bold">{{ optional($item->item)->name ?? 'Product Deleted' }}</td>
                        <td class="text-center fw-bold">{{ number_format($item->quantity) }}</td>
                        <td class="ps-3">{{ $item->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Qty:</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($deliveryChallan->items->sum('quantity')) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@if($deliveryChallan->status === 'pending')
<div class="mt-4">
    <form method="POST" action="{{ route('delivery_challans.create_bill') }}" onsubmit="return confirm('Create a bill from this challan?');">
        @csrf
        <input type="hidden" name="challan_ids[]" value="{{ $deliveryChallan->id }}">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-file-invoice me-1"></i>Convert to Bill
        </button>
    </form>
</div>
@endif
@endsection

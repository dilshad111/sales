@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800"><i class="fas fa-chart-line me-2 text-primary"></i>Sales Report</h1>
    
    <div class="d-flex gap-2">
        <a href="{{ route('reports.sales_pdf', array_merge(request()->query(), ['print' => 1])) }}" target="_blank" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-print me-2"></i>Print
        </a>
        <a href="{{ route('reports.sales_pdf', request()->query()) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
        <a href="{{ route('reports.sales_excel', request()->query()) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<div class="card shadow mb-4 border-0" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-calendar-check me-1"></i>Accounting Year</label>
                    <select name="financial_year_id" class="form-select shadow-sm border-light" onchange="this.form.submit()">
                        <option value="">Custom Range</option>
                        @foreach($financialYears as $fy)
                            <option value="{{ $fy->id }}" {{ ($selectedYearId ?? '') == $fy->id ? 'selected' : '' }}>{{ $fy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-user me-1"></i>Customer</label>
                    <select name="customer_id" class="form-select shadow-sm border-light">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-calendar-alt me-1"></i>Start Date</label>
                    <input type="date" name="start_date" class="form-control shadow-sm border-light" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-calendar-alt me-1"></i>End Date</label>
                    <input type="date" name="end_date" class="form-control shadow-sm border-light" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100 shadow-sm fw-bold" style="height: 38px;"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4 h-100 g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #28a745 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sales Revenue</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalSales, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #17a2b8 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Payments Received</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalPayments, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #fffafa 100%); border-radius: 12px; border-left: 5px solid #ffc107 !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Current Outstanding</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalOutstanding, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold text-gray-800"><i class="fas fa-box me-2 text-primary"></i>Item-wise Sales Analysis</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Item Name</th>
                    <th class="text-end">Quantity Sold</th>
                    <th class="text-end pe-4">Total Sales Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemSales as $itemData)
                <tr>
                    <td class="ps-4 fw-semibold text-dark">{{ $itemData['item']->name }}</td>
                    <td class="text-end fw-bold">{{ number_format($itemData['quantity'], 0) }}</td>
                    <td class="text-end pe-4 fw-bold text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($itemData['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td class="ps-4">TOTAL</td>
                    <td class="text-end">{{ number_format(array_sum(array_column($itemSales, 'quantity')), 0) }}</td>
                    <td class="text-end pe-4 text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format(array_sum(array_column($itemSales, 'total')), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold text-gray-800"><i class="fas fa-file-invoice me-2 text-primary"></i>Detailed Bill Records</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small fw-bold">
                <tr>
                    <th class="ps-4">Bill #</th>
                    <th>Customer Name</th>
                    <th class="text-center">Bill Date</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-end">Amt Paid</th>
                    <th class="text-end pe-4">Current Bal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                <tr>
                    <td class="ps-4 fw-bold text-primary">#{{ $bill->bill_number }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $bill->customer->name }}</div>
                        <div class="small text-muted">Client ID: {{ $bill->customer_id }}</div>
                    </td>
                    <td class="text-center">{{ $bill->bill_date->format('d/m/Y') }}</td>
                    <td class="text-end fw-bold">{{ number_format($bill->total, 2) }}</td>
                    <td class="text-end text-success fw-semibold">{{ number_format($bill->payments->sum('amount'), 2) }}</td>
                    <td class="text-end pe-4 fw-bold text-danger">{{ number_format($bill->total - $bill->payments->sum('amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="3" class="text-end text-uppercase">Total Summary</td>
                    <td class="text-end">{{ number_format($bills->sum('total'), 2) }}</td>
                    <td class="text-end text-success">{{ number_format($bills->sum(fn($b) => $b->payments->sum('amount')), 2) }}</td>
                    <td class="text-end pe-4 text-danger">{{ number_format($bills->sum(fn($b) => $b->total - $b->payments->sum('amount')), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    .table thead th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #777;
        border-bottom: 2px solid #edeff2;
    }
    .text-gray-800 { color: #5a5c69; }
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        transition: all 0.2s ease;
    }
    tfoot {
        border-top: 2px solid #edeff2;
    }
    .table td.text-end {
        white-space: nowrap !important;
    }
</style>
@endsection

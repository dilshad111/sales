@extends('layouts.app')

@section('title', 'Outstanding Payments Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Outstanding Payments Report</h1>
    
    <div class="d-flex gap-2">
        <a href="{{ route('reports.outstanding_payments_pdf', array_merge(request()->query(), ['print' => 1])) }}" target="_blank" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-print me-2"></i>Print
        </a>
        <a href="{{ route('reports.outstanding_payments_pdf', request()->query()) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #ff6a88 0%, #ff3a59 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-file-pdf me-2"></i>Download PDF
        </a>
        <a href="{{ route('reports.outstanding_payments_excel', request()->query()) }}" class="btn shadow-sm fw-bold border-0 text-white" style="background: linear-gradient(135deg, #1d976c 0%, #93f9b9 100%); border-radius: 6px; letter-spacing: 0.5px; padding: 8px 20px;">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<div class="card shadow mb-4 border-0" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
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
                    <label class="form-label fw-bold small text-muted text-uppercase mb-1"><i class="fas fa-info-circle me-1"></i>Status</label>
                    <select name="status" class="form-select shadow-sm border-light">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="outstanding" {{ request('status') == 'outstanding' ? 'selected' : '' }}>Outstanding</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-secondary w-100 shadow-sm fw-bold" style="height: 38px; padding: 0;"><i class="fas fa-filter"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4 h-100 g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #4e73df !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Billed Amt</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_billed'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; border-left: 5px solid #1cc88a !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid (Collected)</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_paid'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #fffafa 100%); border-radius: 12px; border-left: 5px solid #f6c23e !important;">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Outstanding</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_outstanding'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-left: 4px solid #4e73df !important;">
            <div class="card-body py-2 px-3">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.7rem;">1-30 Days</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($agingSummary['1-30'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-left: 4px solid #1cc88a !important;">
            <div class="card-body py-2 px-3">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.7rem;">31-60 Days</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($agingSummary['31-60'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-left: 4px solid #f6c23e !important;">
            <div class="card-body py-2 px-3">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.7rem;">61-90 Days</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($agingSummary['61-90'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-left: 4px solid #e74a3b !important;">
            <div class="card-body py-2 px-3">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.7rem;">91+ Days</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($agingSummary['91+'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Bill #</th>
                    <th>Customer</th>
                    <th class="text-center">Date</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Outstanding</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $data)
                <tr>
                    <td class="ps-4 font-monospace small">{{ $data['bill']->bill_number }}</td>
                    <td class="fw-bold">{{ $data['bill']->customer->name }}</td>
                    <td class="text-center">{{ $data['bill']->bill_date->format('d/m/Y') }}</td>
                    <td class="text-end fw-bold">{{ number_format($data['bill']->total, 2) }}</td>
                    <td class="text-end text-success">{{ number_format($data['paid'], 2) }}</td>
                    <td class="text-end text-danger fw-bold">{{ number_format($data['outstanding'], 2) }}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-3 py-2 bg-{{ $data['status'] == 'paid' ? 'success' : ($data['status'] == 'partially_paid' ? 'warning text-dark' : 'danger') }}">
                            {{ ucfirst(str_replace('_', ' ', $data['status'])) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="3" class="text-end ps-4 text-uppercase small text-muted">Total Current View:</td>
                    <td class="text-end text-primary h5 mb-0">{{ number_format($bills->sum(fn($b) => $b['bill']->total), 2) }}</td>
                    <td class="text-end text-success h5 mb-0">{{ number_format($bills->sum('paid'), 2) }}</td>
                    <td class="text-end text-danger h5 mb-0">{{ number_format($bills->sum('outstanding'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    .table tfoot {
        border-top: 2px solid #edeff2;
    }
    .table td.text-end {
        white-space: nowrap !important;
    }
</style>
@endsection

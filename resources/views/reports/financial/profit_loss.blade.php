@extends('layouts.app')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2 text-success"></i> Income Statement (Profit & Loss)</h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1, 'print' => 1]) }}" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['excel' => 1]) }}" class="btn btn-outline-success"><i class="fas fa-file-excel"></i></a>
                </div>
            </div>
            <form action="{{ route('reports.profit_loss') }}" method="GET" class="d-flex gap-2">
                <select name="financial_year_id" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">-- Custom --</option>
                    @foreach($financialYears as $fy)
                        <option value="{{ $fy->id }}" {{ $selectedYearId == $fy->id ? 'selected' : '' }}>
                            {{ $fy->name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate->toDateString() }}">
                <span class="align-self-center text-muted">to</span>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate->toDateString() }}">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body py-4">
            <div class="row">
                <!-- Income Section -->
                <div class="col-md-6 mb-4">
                    <h6 class="fw-bold text-uppercase ls-1 text-success border-bottom pb-2">Operating Income</h6>
                    <table class="table table-borderless table-sm">
                        @foreach($incomeAccounts as $data)
                        <tr>
                            <td>{{ $data->name }}</td>
                            <td class="text-end fw-semibold">{{ number_format(abs($data->net_change), 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-top">
                            <td class="fw-bold pt-2">TOTAL INCOME</td>
                            <td class="text-end fw-bold pt-2">{{ number_format(abs($totalIncome), 2) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Expense Section -->
                <div class="col-md-6 mb-4">
                    <h6 class="fw-bold text-uppercase ls-1 text-danger border-bottom pb-2">Operating Expenses</h6>
                    <table class="table table-borderless table-sm">
                        @foreach($expenseAccounts as $data)
                        <tr>
                            <td>{{ $data->name }}</td>
                            <td class="text-end fw-semibold text-danger">{{ number_format($data->net_change, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-top">
                            <td class="fw-bold pt-2">TOTAL EXPENSES</td>
                            <td class="text-end fw-bold pt-2 text-danger">({{ number_format($totalExpense, 2) }})</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Net Profit Summary Card -->
            <div class="mt-4 p-4 rounded-3 {{ $netProfit >= 0 ? 'bg-label-success' : 'bg-label-danger' }} border">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-1">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</h4>
                        <p class="mb-0 opacity-75">Summary for period {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <h2 class="fw-bold mb-0">{{ number_format(abs($netProfit), 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

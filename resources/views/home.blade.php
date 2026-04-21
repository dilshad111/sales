@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Add Google Font: Outfit -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 1.25rem; background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%) !important;">
            <div class="card-body p-4 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-800 mb-1 text-white tracking-tight" style="font-family: 'Outfit', sans-serif;">Welcome Back, {{ auth()->user()->name }}! 👋</h4>
                        <p class="mb-0 opacity-75 small text-white-50">Your sales dashboard is optimized and up to date.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <div class="d-inline-flex align-items-center px-3 py-2 rounded-3" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <i class="far fa-calendar-alt me-2 small text-white opacity-50"></i>
                            <span class="small fw-semibold text-white tracking-tight"> {{ now()->format('l, d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative Elements -->
            <div class="position-absolute top-0 end-0 p-4 opacity-10">
                <i class="fas fa-chart-line fa-5x"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Statistics -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-up bg-white rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-label-primary p-3 rounded-4">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div class="badge bg-label-success rounded-pill px-3 py-2">
                        <i class="fas fa-arrow-up me-1 small"></i> 12%
                    </div>
                </div>
                <div class="mb-1">
                    <span class="text-muted text-uppercase fw-bold ls-1 small-85">Lifetime Sales</span>
                </div>
                <div class="d-flex align-items-baseline flex-wrap">
                    <span class="currency-symbol me-1 text-primary opacity-75 fw-medium">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                    <h3 class="mb-0 fw-800 tracking-tight amount-display">{{ number_format($stats['total_sales'], 2) }}</h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-up bg-white rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-label-info p-3 rounded-4">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <div class="badge bg-label-info rounded-pill px-3 py-2">
                        Performance
                    </div>
                </div>
                <div class="mb-1">
                    <span class="text-muted text-uppercase fw-bold ls-1 small-85">Items Sold</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-800 tracking-tight amount-display">{{ number_format($stats['total_items_sold']) }}</h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-up bg-white rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-label-warning p-3 rounded-4">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div class="badge bg-label-danger rounded-pill px-3 py-2">
                        Action Required
                    </div>
                </div>
                <div class="mb-1">
                    <span class="text-muted text-uppercase fw-bold ls-1 small-85">Outstanding</span>
                </div>
                <div class="d-flex align-items-baseline flex-wrap">
                    <span class="currency-symbol me-1 text-danger opacity-75 fw-medium">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                    <h3 class="mb-0 fw-800 tracking-tight amount-display text-danger">{{ number_format($stats['outstanding'], 2) }}</h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-up bg-white rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-label-success p-3 rounded-4">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div class="badge bg-label-success rounded-pill px-3 py-2">
                        +3 New
                    </div>
                </div>
                <div class="mb-1">
                    <span class="text-muted text-uppercase fw-bold ls-1 small-85">Total Customers</span>
                </div>
                <div>
                    <h3 class="mb-0 fw-800 tracking-tight amount-display">{{ number_format($stats['customers']) }}</h3>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Section -->
<div class="row g-4 mb-4">
    <!-- Monthly Sales Trend (Item-wise) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">Total Monthly Sales (Item-wise)</h5>
                    <small class="text-muted">Calculated from individual line items revenue</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button">
                        Last 12 Months
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="itemWiseSalesChart" height="320"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Selling Items -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-4">
                <h5 class="mb-0 fw-bold">Top Selling Items</h5>
                <small class="text-muted">Revenue contribution by product</small>
            </div>
            <div class="card-body p-0">
                <div class="px-4 pb-4">
                    <canvas id="topItemsChart" height="250"></canvas>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr class="small text-muted">
                                <th class="ps-4">Item Name</th>
                                <th class="text-end pe-4">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topItems as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-label-primary p-2 rounded-circle me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-box small"></i>
                                        </div>
                                        <span class="fw-semibold text-dark small">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($item->bill_items_sum_total, 0) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Sales vs Payments Comparison -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4">
                <h5 class="mb-0 fw-bold">Cash Flow Matrix</h5>
                <small class="text-muted">Comparing sales revenue vs collections</small>
            </div>
            <div class="card-body">
                <canvas id="cashFlowChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers List -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Key Accounts</h5>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($topCustomers as $customer)
                    <div class="list-group-item px-0 py-3 border-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-label-primary me-3">
                                <span class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $customer->name }}</h6>
                                <small class="text-muted">Total Bills: {{ $customer->bills_count ?? $customer->bills()->count() }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($customer->bills_sum_total, 0) }}</div>
                            <small class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> Active Repo</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aging Analysis Row -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">Outstanding Aging Analysis</h5>
                    <small class="text-muted">Breakdown of receivables by age of debt</small>
                </div>
                <div class="badge bg-label-secondary rounded-pill px-3 py-2">
                    Global Overview
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 border border-light bg-light bg-opacity-25 text-center transition-all hover-up-small">
                            <div class="text-muted small text-uppercase fw-bold mb-2">1-30 Days</div>
                            <h4 class="mb-1 fw-800 text-primary">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($agingSummary['1-30'], 0) }}</h4>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 border border-light bg-light bg-opacity-25 text-center transition-all hover-up-small">
                            <div class="text-muted small text-uppercase fw-bold mb-2">31-60 Days</div>
                            <h4 class="mb-1 fw-800 text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($agingSummary['31-60'], 0) }}</h4>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 border border-light bg-light bg-opacity-25 text-center transition-all hover-up-small">
                            <div class="text-muted small text-uppercase fw-bold mb-2">61-90 Days</div>
                            <h4 class="mb-1 fw-800 text-warning">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($agingSummary['61-90'], 0) }}</h4>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 border border-light bg-light bg-opacity-25 text-center transition-all hover-up-small">
                            <div class="text-muted small text-uppercase fw-bold mb-2">91+ Days</div>
                            <h4 class="mb-1 fw-800 text-danger">{{ $companySetting->currency_symbol ?? 'Rs.' }}{{ number_format($agingSummary['91+'], 0) }}</h4>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Quick Actions -->
<div class="row g-3 pb-5">
    <div class="col-6 col-md-3">
        <a href="{{ route('bills.create') }}" class="btn btn-primary w-100 rounded-4 py-3 shadow border-0 hover-up">
            <i class="fas fa-file-invoice mb-2 d-block fa-lg"></i>
            <span class="small fw-bold">New Invoice</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('payments.create') }}" class="btn btn-success w-100 rounded-4 py-3 shadow border-0 hover-up">
            <i class="fas fa-hand-holding-dollar mb-2 d-block fa-lg"></i>
            <span class="small fw-bold">Rec. Payment</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('customers.create') }}" class="btn btn-info text-white w-100 rounded-4 py-3 shadow border-0 hover-up">
            <i class="fas fa-user-plus mb-2 d-block fa-lg"></i>
            <span class="small fw-bold">Add Client</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('reports.sales') }}" class="btn btn-warning text-white w-100 rounded-4 py-3 shadow border-0 hover-up">
            <i class="fas fa-chart-bar mb-2 d-block fa-lg"></i>
            <span class="small fw-bold">Sales Report</span>
        </a>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "'Public Sans', sans-serif";
        Chart.defaults.color = '#8e98a8';
        
        const currency = '{{ $companySetting->currency_symbol ?? 'Rs.' }}';

        // Item-wise Monthly Sales Chart
        const ctxItemSales = document.getElementById('itemWiseSalesChart').getContext('2d');
        new Chart(ctxItemSales, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Item-wise Sales',
                        data: @json($itemWiseSalesData),
                        borderColor: '#696cff',
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(105, 108, 255, 0.05)');
                            gradient.addColorStop(1, 'rgba(105, 108, 255, 0.4)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#696cff',
                        pointBorderWidth: 2,
                        hitRadius: 10,
                        borderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#566a7f',
                        bodyColor: '#566a7f',
                        borderColor: '#e1e5eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: (context) => `Revenue: ${currency} ${(context.parsed.y/1000000).toFixed(2)}M`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f0f2f4', drawBorder: false },
                        ticks: { padding: 10, callback: (value) => currency + ' ' + (value/1000000).toFixed(1) + 'M' }
                    },
                    x: { grid: { display: false, drawBorder: false }, ticks: { padding: 10 } }
                }
            }
        });

        // Top Selling Items Doughnut
        const ctxTopItems = document.getElementById('topItemsChart').getContext('2d');
        new Chart(ctxTopItems, {
            type: 'doughnut',
            data: {
                labels: @json($topItems->pluck('name')),
                datasets: [{
                    data: @json($topItems->pluck('bill_items_sum_total')),
                    backgroundColor: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d'],
                    hoverOffset: 15,
                    borderWidth: 5,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Cash Flow Comparison Chart (Bar)
        const ctxCashFlow = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(ctxCashFlow, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Sales',
                        data: @json($salesData),
                        backgroundColor: '#696cff',
                        borderRadius: 8,
                        barThickness: 15
                    },
                    {
                        label: 'Payments',
                        data: @json($paymentsData),
                        backgroundColor: '#03c3ec',
                        borderRadius: 8,
                        barThickness: 15
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }
                },
                scales: {
                    y: { grid: { display: false }, ticks: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush

<style>
    body { font-family: 'Public Sans', sans-serif; }
    .amount-display { 
        font-family: 'Outfit', sans-serif; 
        font-size: 1.85rem; 
        transition: all 0.3s ease;
    }
    .fw-800 { font-weight: 800; }
    .tracking-tight { letter-spacing: -0.025em; }
    .small-85 { font-size: 0.85rem; }
    
    .rounded-4 { border-radius: 1rem !important; }
    .rounded-5 { border-radius: 1.5rem !important; }
    .hover-up { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-up:hover { transform: translateY(-8px); box-shadow: 0 1.5rem 3rem rgba(105, 108, 255, 0.15) !important; }
    .backdrop-blur { backdrop-filter: blur(10px); }
    .ls-1 { letter-spacing: 1px; }
    
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-info { background-color: #e1f0ff !important; color: #03c3ec !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    
    .card-footer .progress {
        background-color: #f0f2f4;
        border-radius: 10px;
    }
    
    .stat-card .card-body {
        position: relative;
        z-index: 1;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card { animation: fadeInUp 0.5s ease-out forwards; }
    
    canvas { width: 100% !important; }
    
    .hover-up-small { transition: all 0.2s ease; }
    .hover-up-small:hover { transform: translateY(-3px); border-color: #696cff !important; background: #fff !important; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .bg-label-secondary { background-color: #f0f2f4 !important; color: #8e98a8 !important; }
</style>
@endsection

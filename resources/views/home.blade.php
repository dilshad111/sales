@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5 fw-bold mb-2"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard Overview</h1>
        <p class="text-muted">Real-time performance metrics and financial summaries.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Customers</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['customers'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-box fa-2x text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Items</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['items'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-file-invoice fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Bills</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['bills'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-credit-card fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Payments</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['payments'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Sales vs Payments Trend -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Sales & Payments Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="salesTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-crown me-2 text-warning"></i>Top 5 Customers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Name</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $customer->name }}</div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ₨{{ number_format($customer->bills_sum_total, 0) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <canvas id="topCustomersChart" height="250" class="mt-3"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary & Actions -->
<div class="row mb-3 text-center">
    <div class="col-md-6 mb-3">
        <div class="bg-primary bg-opacity-10 rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
            <h6 class="text-primary text-uppercase fw-bold mb-3 small">Total Lifetime Revenue</h6>
            <h2 class="fw-bold text-primary mb-0">₨{{ number_format($stats['total_sales'], 2) }}</h2>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="bg-success bg-opacity-10 rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
            <h6 class="text-success text-uppercase fw-bold mb-3 small">Total Lifetime Collection</h6>
            <h2 class="fw-bold text-success mb-0">₨{{ number_format($stats['total_payments'], 2) }}</h2>
        </div>
    </div>
</div>

<div class="row g-3 pb-4">
    <div class="col-md-3">
        <a href="{{ route('bills.create') }}" class="btn btn-primary w-100 rounded-3 py-3 shadow-sm border-0 transition-hover">
            <i class="fas fa-plus-circle mb-2 d-block fa-2x"></i>
            Create New Bill
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('payments.create') }}" class="btn btn-success w-100 rounded-3 py-3 shadow-sm border-0 transition-hover">
            <i class="fas fa-hand-holding-dollar mb-2 d-block fa-2x"></i>
            Record Payment
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('items.create') }}" class="btn btn-warning w-100 rounded-3 py-3 shadow-sm border-0 transition-hover text-white">
            <i class="fas fa-box-open mb-2 d-block fa-2x"></i>
            Add New Item
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.cash_statement') }}" class="btn btn-info w-100 rounded-3 py-3 shadow-sm border-0 transition-hover text-white">
            <i class="fas fa-file-invoice-dollar mb-2 d-block fa-2x"></i>
            Cash Statement
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sales vs Payments Trend Chart
        const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Sales Revenue',
                        data: @json($salesData),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#4e73df'
                    },
                    {
                        label: 'Payments Received',
                        data: @json($paymentsData),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#1cc88a'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            callback: function(value) { return '₨' + value.toLocaleString(); }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Top Customers Chart
        const ctxCustomers = document.getElementById('topCustomersChart').getContext('2d');
        new Chart(ctxCustomers, {
            type: 'doughnut',
            data: {
                labels: @json($topCustomers->pluck('name')),
                datasets: [{
                    data: @json($topCustomers->pluck('bills_sum_total')),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    ],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                },
                cutout: '70%'
            }
        });
    });
</script>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .rounded-4 { border-radius: 1rem !important; }
    canvas { max-height: 350px !important; }
    #salesTrendChart { max-height: 300px !important; }
    #topCustomersChart { max-height: 250px !important; }
</style>
@endsection

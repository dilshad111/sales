@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-balance-scale me-2 text-primary"></i> Statement of Financial Position (Balance Sheet)</h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1, 'print' => 1]) }}" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['excel' => 1]) }}" class="btn btn-outline-success"><i class="fas fa-file-excel"></i></a>
                </div>
            </div>
            <form action="{{ route('reports.balance_sheet') }}" method="GET" class="d-flex gap-2">
                <select name="financial_year_id" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">-- Active View --</option>
                    @foreach($financialYears as $fy)
                        <option value="{{ $fy->id }}" {{ $selectedYearId == $fy->id ? 'selected' : '' }}>
                            {{ $fy->name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date" class="form-control" value="{{ $date->toDateString() }}">
                <button type="submit" class="btn btn-primary">Refresh</button>
            </form>
        </div>
        <div class="card-body py-5">
            <div class="row g-5">
                <!-- Assets Side -->
                <div class="col-lg-6">
                    <div class="p-4 bg-light bg-opacity-50 rounded-4 h-100">
                        <h5 class="fw-bold text-uppercase ls-1 text-primary mb-4 border-bottom pb-2">Assets</h5>
                        <table class="table table-borderless table-sm">
                            @php $totalAssets = 0; @endphp
                            @foreach($assets as $a)
                                @php $totalAssets += $a['balance']; @endphp
                                <tr>
                                    <td class="py-2">{{ $a['name'] }}</td>
                                    <td class="text-end py-2 fw-semibold">{{ number_format($a['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-top" style="border-width: 2px !important;">
                                <td class="fw-bold py-3 h5 mb-0">TOTAL ASSETS</td>
                                <td class="text-end fw-bold py-3 h5 mb-0 text-primary">{{ number_format($totalAssets, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Liabilities & Equity Side -->
                <div class="col-lg-6">
                    <div class="p-4 bg-white border rounded-4 shadow-sm mb-4">
                        <h5 class="fw-bold text-uppercase ls-1 text-danger mb-4 border-bottom pb-2">Liabilities</h5>
                        <table class="table table-borderless table-sm">
                            @php $totalLiabilities = 0; @endphp
                            @foreach($liabilities as $l)
                                @php $totalLiabilities += abs($l['balance']); @endphp
                                <tr>
                                    <td class="py-2">{{ $l['name'] }}</td>
                                    <td class="text-end py-2 fw-semibold">{{ number_format(abs($l['balance']), 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-top">
                                <td class="fw-bold py-2">Total Liabilities</td>
                                <td class="text-end fw-bold py-2">{{ number_format($totalLiabilities, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="p-4 bg-white border rounded-4 shadow-sm">
                        <h5 class="fw-bold text-uppercase ls-1 text-warning mb-4 border-bottom pb-2">Equity & Retained Earnings</h5>
                        <table class="table table-borderless table-sm">
                            @php $totalEquity = 0; @endphp
                            @foreach($equity as $e)
                                @php $totalEquity += abs($e['balance']); @endphp
                                <tr>
                                    <td class="py-2">{{ $e['name'] }}</td>
                                    <td class="text-end py-2 fw-semibold">{{ number_format(abs($e['balance']), 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="py-2 italic">Current Period Profit/Loss</td>
                                <td class="text-end py-2 fw-semibold {{ $retainedEarnings >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($retainedEarnings, 2) }}
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold py-2">Total Equity</td>
                                <td class="text-end fw-bold py-2">{{ number_format($totalEquity + $retainedEarnings, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-4 p-4 bg-dark rounded-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 fw-bold">TOTAL LIABILITIES & EQUITY</span>
                            <span class="h4 mb-0 fw-bold text-warning">{{ number_format($totalLiabilities + $totalEquity + $retainedEarnings, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Check -->
            @php $diff = $totalAssets - ($totalLiabilities + $totalEquity + $retainedEarnings); @endphp
            @if(abs($diff) > 0.01)
                <div class="alert bg-label-danger mt-5 border d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fa-2x"></i>
                    <div>
                        <div class="fw-bold">Warning: Account Imbalance</div>
                        <div>Your Balance Sheet is not equal. Difference: {{ number_format(abs($diff), 2) }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

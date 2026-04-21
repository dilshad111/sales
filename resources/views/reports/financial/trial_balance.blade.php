@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-scale-balanced me-2 text-primary"></i> Trial Balance</h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1, 'print' => 1]) }}" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['excel' => 1]) }}" class="btn btn-outline-success"><i class="fas fa-file-excel"></i></a>
                </div>
            </div>
            <form action="{{ route('reports.trial_balance') }}" method="GET" class="d-flex gap-2">
                <select name="financial_year_id" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                    <option value="">-- Active View --</option>
                    @foreach($financialYears as $fy)
                        <option value="{{ $fy->id }}" {{ $selectedYearId == $fy->id ? 'selected' : '' }}>
                            {{ $fy->name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date->toDateString() }}">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Account Name</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end pe-4">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebit = 0; $totalCredit = 0; @endphp
                        @foreach($accounts as $data)
                            @php 
                                $totalDebit += $data['debit']; 
                                $totalCredit += $data['credit']; 
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small font-monospace">{{ $data['account']->code ?: '---' }}</td>
                                <td class="fw-bold">{{ $data['account']->name }}</td>
                                <td class="text-end text-primary fw-semibold">
                                    {{ $data['debit'] > 0 ? number_format($data['debit'], 2) : '-' }}
                                </td>
                                <td class="text-end text-danger fw-semibold pe-4">
                                    {{ $data['credit'] > 0 ? number_format($data['credit'], 2) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-white border-top">
                        <tr class="fw-bold h5">
                            <td colspan="2" class="text-end ps-4 border-end">TOTAL</td>
                            <td class="text-end text-primary border-end bg-light bg-opacity-10">{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end text-danger pe-4 bg-light bg-opacity-10">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if(abs($totalDebit - $totalCredit) > 0.01)
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0">
        <i class="fas fa-triangle-exclamation me-3 fa-2x"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Unbalanced Trial Balance!</h6>
            <span>Discrepancy of <strong>{{ number_format(abs($totalDebit - $totalCredit), 2) }}</strong> detected. Please check your journal entries.</span>
        </div>
    </div>
    @else
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0">
        <i class="fas fa-circle-check me-3 fa-2x"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Perfectly Balanced</h6>
            <span>The total debits equal the total credits. Your ledger is in equilibrium.</span>
        </div>
    </div>
    @endif
</div>
@endsection

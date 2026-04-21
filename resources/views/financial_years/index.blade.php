@extends('layouts.app')

@section('title', 'Financial Year Management')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Year Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFyModal">
            <i class="fas fa-plus me-1"></i> New Financial Year
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">FY Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Closed At</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financialYears as $fy)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">{{ $fy->name }}</span>
                        </td>
                        <td>{{ $fy->start_date->format('d M Y') }}</td>
                        <td>{{ $fy->end_date->format('d M Y') }}</td>
                        <td>
                            @if($fy->is_closed)
                                <span class="badge bg-danger">Closed</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            @if($fy->closed_at)
                                <small class="text-muted">{{ $fy->closed_at->format('d M Y H:i') }}</small>
                                <br>
                                <small class="text-xs">By: {{ $fy->closedBy->name ?? 'System' }}</small>
                            @else
                                <span class="text-muted opacity-50">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                @if(!$fy->is_closed)
                                    <form action="{{ route('financial_years.close', $fy) }}" method="POST" onsubmit="return confirm('Are you sure you want to CLOSE this financial year? This will generate opening balances for the next year and lock all transactions.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-lock me-1"></i> Close Year
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('financial_years.reopen', $fy) }}" method="POST" onsubmit="return confirm('Are you sure you want to REOPEN this financial year?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-unlock me-1"></i> Reopen
                                        </button>
                                    </form>
                                @endif
                                <button class="btn btn-sm btn-light border ms-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No financial years defined yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create FY Modal -->
<div class="modal fade" id="createFyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('financial_years.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Financial Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">FY Name <small>(e.g. FY 2025-26)</small></label>
                        <input type="text" name="name" class="form-control" required placeholder="FY 2025-26">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Year</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

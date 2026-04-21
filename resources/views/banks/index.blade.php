@extends('layouts.app')

@section('title', 'Banks')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Settings /</span> Banks
    </h4>
    <a href="{{ route('banks.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Add Bank
    </a>
</div>

<!-- Banks List Card -->
<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Bank Name</th>
                    <th>Account Number</th>
                    <th>Branch</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($banks as $bank)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">{{ $bank->name }}</span>
                    </td>
                    <td>{{ $bank->account_number ?? '-' }}</td>
                    <td>{{ $bank->branch ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-label-{{ $bank->status == 'active' ? 'success' : 'danger' }} rounded-pill">
                            {{ ucfirst($bank->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('banks.edit', $bank) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit Bank">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('banks.destroy', $bank) }}" class="d-inline">
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
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">No banks found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($banks->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $banks->links() !!}
        </div>
    </div>
    @endif
</div>
@endsection

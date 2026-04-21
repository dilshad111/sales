@extends('layouts.app')

@section('title', 'Agents Master')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Purchases /</span> Agent Master
    </h4>
    <a href="{{ route('agents.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Add Agent
    </a>
</div>

<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Agent Name</th>
                    <th>Phone</th>
                    <th class="text-center">Commission %</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($agents as $agent)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">{{ $agent->name }}</span>
                        @if($agent->email)
                            <div class="small text-muted">{{ $agent->email }}</div>
                        @endif
                    </td>
                    <td>{{ $agent->phone ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-label-info">{{ number_format($agent->commission_percentage, 2) }}%</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-{{ $agent->status == 'active' ? 'success' : 'danger' }} rounded-pill">
                            {{ ucfirst($agent->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('agents.edit', $agent) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit Agent">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('agents.destroy', $agent) }}" class="d-inline">
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
                        <div class="text-muted">No agents found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agents->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $agents->links() !!}
        </div>
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history me-2 text-info"></i>Audit Logs</h1>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('settings.audits.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Search Logs</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Search by model, event, or IP...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('settings.audits.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 text-center">Date & Time</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Model</th>
                        <th class="text-center">IP Address</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                    <tr>
                        <td class="ps-3 text-center small">{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $audit->user->name ?? 'System' }}</div>
                            <div class="small text-muted">{{ $audit->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($audit->event) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'danger',
                                    default => 'info'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} rounded-pill px-3">{{ strtoupper($audit->event) }}</span>
                        </td>
                        <td>
                            <code class="text-secondary small">{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</code>
                        </td>
                        <td class="text-center small text-muted">{{ $audit->ip_address }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#auditModal{{ $audit->id }}">
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>

                    <!-- Audit Modal -->
                    <div class="modal fade" id="auditModal{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Audit Details #{{ $audit->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>User:</strong> {{ $audit->user->name ?? 'System' }}</p>
                                            <p><strong>Event:</strong> {{ strtoupper($audit->event) }}</p>
                                            <p><strong>Model:</strong> {{ $audit->auditable_type }} (#{{ $audit->auditable_id }})</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date:</strong> {{ $audit->created_at->format('d/m/Y H:i:s') }}</p>
                                            <p><strong>IP:</strong> {{ $audit->ip_address }}</p>
                                            <p><strong>Agent:</strong> <small class="text-muted">{{ $audit->user_agent }}</small></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Old Values</h6>
                                            <pre class="bg-light p-3 rounded small">@json($audit->old_values, JSON_PRETTY_PRINT)</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>New Values</h6>
                                            <pre class="bg-light p-3 rounded small">@json($audit->new_values, JSON_PRETTY_PRINT)</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No audit logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($audits->hasPages())
    <div class="card-footer bg-white">
        {{ $audits->links() }}
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Personal Accounts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0"><i class="fas fa-wallet me-2"></i>Personal Accounts</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#commissionModal">
            <i class="fas fa-plus me-1"></i>Record Commission
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="fas fa-money-bill-wave me-1"></i>Record Payment
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Individual</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name">
            </div>
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" name="from_date" class="form-control" value="{{ $filters['from_date'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" name="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-secondary"><i class="fas fa-filter me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Individual</th>
                        <th class="text-end">Total Commission (₨)</th>
                        <th class="text-end">Total Payments (₨)</th>
                        <th class="text-end">Balance (₨)</th>
                        <th class="text-center" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $commissionTotal = (float) ($user->commission_sum ?? 0);
                            $paymentTotal = (float) ($user->payment_sum ?? 0);
                            $balance = $commissionTotal - $paymentTotal;
                            $userOutstanding = $openCommissions->get($user->id) ?? collect();
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <div class="small text-muted">Email: {{ $user->email }}</div>
                                @if($userOutstanding->isNotEmpty())
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-info-circle me-1"></i>Outstanding commissions: {{ number_format($userOutstanding->sum('outstanding'), 2) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($commissionTotal, 2) }}</td>
                            <td class="text-end">{{ number_format($paymentTotal, 2) }}</td>
                            <td class="text-end fw-semibold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('personal_accounts.statement', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-alt me-1"></i>Statement
                                </a>
                                <button class="btn btn-sm btn-outline-success mt-1 mt-lg-0 record-payment-btn"
                                    data-bs-toggle="modal" data-bs-target="#paymentModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}">
                                    <i class="fas fa-money-bill me-1"></i>Payment
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    @endif
</div>

@php
    $outstandingMap = $openCommissions->mapWithKeys(function ($commissions, $userId) {
        return [
            $userId => $commissions->map(function ($commission) {
                return [
                    'id' => $commission->id,
                    'label' => ($commission->reference ?: ('Commission #' . $commission->id)) . ' - Outstanding ₨' . number_format($commission->outstanding, 2),
                ];
            })->values(),
        ];
    });
@endphp

<!-- Commission Modal -->
<div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionModalLabel">Record Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('personal_accounts.commissions.store') }}" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="commission_user_id" class="form-label">Individual</label>
                        <select id="commission_user_id" name="user_id" class="form-select" required>
                            <option value="">Select Individual</option>
                            @foreach($usersForForms as $userOption)
                                <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="commission_date" class="form-label">Commission Date</label>
                        <input type="date" id="commission_date" name="commission_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="commission_amount" class="form-label">Amount (₨)</label>
                        <input type="number" step="0.01" min="0" id="commission_amount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="commission_reference" class="form-label">Reference</label>
                        <input type="text" id="commission_reference" name="reference" class="form-control" placeholder="Optional reference">
                    </div>
                    <div class="mb-3">
                        <label for="commission_notes" class="form-label">Notes</label>
                        <textarea id="commission_notes" name="notes" class="form-control" rows="3" placeholder="Additional details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Commission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('personal_accounts.payments.store') }}" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_user_id" class="form-label">Individual</label>
                        <select id="payment_user_id" name="user_id" class="form-select" required>
                            <option value="">Select Individual</option>
                            @foreach($usersForForms as $userOption)
                                <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_commission_id" class="form-label">Apply To Commission (Optional)</label>
                        <select id="payment_commission_id" name="commission_id" class="form-select">
                            <option value="">Unassigned Payment</option>
                        </select>
                        <div class="form-text">Outstanding commissions for the selected individual will appear here.</div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Amount (₨)</label>
                        <input type="number" step="0.01" min="0" id="payment_amount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label">Reference</label>
                        <input type="text" id="payment_reference" name="reference" class="form-control" placeholder="Optional reference">
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea id="payment_notes" name="notes" class="form-control" rows="3" placeholder="Additional details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const outstandingCommissions = @json($outstandingMap);
        const paymentModal = document.getElementById('paymentModal');
        const paymentUserSelect = document.getElementById('payment_user_id');
        const paymentCommissionSelect = document.getElementById('payment_commission_id');

        const populateCommissionOptions = (userId) => {
            paymentCommissionSelect.innerHTML = '<option value="">Unassigned Payment</option>';
            if (!userId || !outstandingCommissions[userId]) {
                return;
            }

            outstandingCommissions[userId].forEach((commission) => {
                const option = document.createElement('option');
                option.value = commission.id;
                option.textContent = commission.label;
                paymentCommissionSelect.appendChild(option);
            });
        };

        paymentUserSelect.addEventListener('change', (event) => {
            populateCommissionOptions(event.target.value);
        });

        paymentModal.addEventListener('show.bs.modal', (event) => {
            const triggerButton = event.relatedTarget;
            if (!triggerButton) {
                paymentUserSelect.value = '';
                populateCommissionOptions(null);
                return;
            }

            const userId = triggerButton.getAttribute('data-user-id');
            if (userId) {
                paymentUserSelect.value = userId;
                populateCommissionOptions(userId);
            }
        });
    })();
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Delivery Challans')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck me-2 text-primary"></i>Delivery Challans</h1>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" id="createChallanDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i>Create Challan
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="createChallanDropdown">
            <li>
                <a class="dropdown-item py-2" href="{{ route('delivery_challans.create') }}">
                    <i class="fas fa-edit me-2 text-primary"></i>Direct Delivery Challan
                </a>
            </li>
            <li>
                <a class="dropdown-item py-2" href="{{ route('delivery_challans.select_so') }}">
                    <i class="fas fa-file-invoice me-2 text-info"></i>DC from Sales Order
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="billed" {{ request('status') === 'billed' ? 'selected' : '' }}>Billed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Show</label>
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 Records</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Create Bill from Selected Challans --}}
<form id="createBillForm" method="POST" action="{{ route('delivery_challans.create_bill') }}">
    @csrf
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
            <span class="fw-bold text-muted small"><i class="fas fa-list me-1"></i>CHALLAN LIST</span>
            <button type="submit" class="btn btn-success btn-sm" id="createBillBtn" disabled>
                <i class="fas fa-file-invoice me-1"></i>Create Bill from Selected (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" title="Select All Pending">
                            </th>
                            <th>Challan #</th>
                            <th>Customer</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Vehicle</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans as $challan)
                        <tr>
                            <td class="ps-3">
                                @if($challan->status === 'pending')
                                <input type="checkbox" class="form-check-input challan-checkbox" name="challan_ids[]" value="{{ $challan->id }}" data-customer="{{ $challan->customer_id }}">
                                @endif
                            </td>
                            <td><code class="text-primary fw-bold">{{ $challan->challan_number }}</code></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $challan->customer ? $challan->customer->name : 'Customer Deleted' }}</div>
                            </td>
                            <td class="text-center">{{ $challan->challan_date->format('d/m/Y') }}</td>
                            <td class="text-center"><span class="small text-muted">{{ $challan->vehicle_number ?? '-' }}</span></td>
                            <td class="text-end fw-bold">₨{{ number_format($challan->total, 2) }}</td>
                            <td class="text-center">
                                @if($challan->status === 'pending')
                                    <span class="badge bg-label-warning">Pending</span>
                                @else
                                    @if($challan->bill_id)
                                        <a href="{{ route('bills.show', $challan->bill_id) }}" class="badge bg-label-success text-decoration-none" title="View Bill {{ optional($challan->bill)->bill_number }}">
                                            Billed
                                        </a>
                                    @else
                                        <span class="badge bg-label-success">Billed</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('delivery_challans.show', $challan) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('delivery_challans.print', $challan) }}" class="btn btn-outline-secondary" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($challan->status === 'pending')
                                    <a href="{{ route('delivery_challans.edit', $challan) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Use JS for delete to avoid nested form issues --}}
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ route('delivery_challans.destroy', $challan) }}')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-truck fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No delivery challans found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($challans->hasPages())
        <div class="card-footer bg-white">
            {{ $challans->links() }}
        </div>
        @endif
    </div>
</form>

{{-- Single hidden form for deletions --}}
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this record?')) {
        const form = document.getElementById('deleteForm');
        form.action = url;
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.challan-checkbox');
    const createBillBtn = document.getElementById('createBillBtn');
    const selectedCount = document.getElementById('selectedCount');
    const form = document.getElementById('createBillForm');

    const updateUI = () => {
        const checked = document.querySelectorAll('.challan-checkbox:checked');
        selectedCount.textContent = checked.length;
        createBillBtn.disabled = checked.length === 0;

        if (checked.length > 0) {
            const customers = new Set();
            checked.forEach(cb => customers.add(cb.dataset.customer));
            if (customers.size > 1) {
                createBillBtn.disabled = true;
                createBillBtn.title = 'All selected challans must belong to the same customer';
                createBillBtn.classList.add('btn-secondary');
                createBillBtn.classList.remove('btn-success');
            } else {
                createBillBtn.title = '';
                createBillBtn.classList.remove('btn-secondary');
                createBillBtn.classList.add('btn-success');
            }
        } else {
            createBillBtn.title = '';
            createBillBtn.classList.remove('btn-secondary');
            createBillBtn.classList.add('btn-success');
        }
    };

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateUI();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    form.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.challan-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one pending challan.');
            return;
        }
        const customers = new Set();
        checked.forEach(cb => customers.add(cb.dataset.customer));
        if (customers.size > 1) {
            e.preventDefault();
            alert('All selected challans must belong to the same customer.');
            return;
        }
        if (!confirm('Create a bill from ' + checked.length + ' selected challan(s)?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection

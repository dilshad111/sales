@extends('layouts.app')

@section('title', 'Add COA Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="fas fa-plus-circle me-2 text-info"></i>Add COA Account
                </h5>
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label text-muted small font-weight-bold mb-2">Quick Presets (Root Accounts)</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill preset-btn" data-name="Assets" data-code="1000" data-type="Asset">Asset 1000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill preset-btn" data-name="Liabilities" data-code="2000" data-type="Liability">Liability 2000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill preset-btn" data-name="Income" data-code="3000" data-type="Income">Income 3000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill preset-btn" data-name="Expenses" data-code="4000" data-type="Expense">Expense 4000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill preset-btn" data-name="Equity" data-code="5000" data-type="Equity">Equity 5000</button>
                        </div>
                        <div class="form-text mt-1 font-10"><i class="fas fa-magic me-1"></i> Clicking a preset will pre-fill standard root account details.</div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control bg-light @error('name') is-invalid @enderror" placeholder="e.g. Office Rent, HBL Branch" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold">Account Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control bg-light @error('code') is-invalid @enderror" placeholder="e.g. 1101" value="{{ old('code', $suggestedCode) }}" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold">Parent Account (Group)</label>
                            <select name="parent_id" id="parent_id" class="form-select bg-light select2 @error('parent_id') is-invalid @enderror" onchange="updateAccountType()">
                                <option value="">None (Top Level)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" data-type="{{ $parent->type }}" {{ (old('parent_id', $selectedParentId) == $parent->id) ? 'selected' : '' }}>
                                        {{ $parent->code }} - {{ $parent->name }} ({{ $parent->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text small mt-1 text-info"><i class="fas fa-info-circle me-1"></i>New account will inherit accounting type from parent.</div>
                        </div>

                        <div class="col-md-4" id="type_container">
                            <label class="form-label text-muted small font-weight-bold">Accounting Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select bg-light @error('type') is-invalid @enderror">
                                <option value="Asset" {{ old('type') == 'Asset' ? 'selected' : '' }}>Asset</option>
                                <option value="Liability" {{ old('type') == 'Liability' ? 'selected' : '' }}>Liability</option>
                                <option value="Equity" {{ old('type') == 'Equity' ? 'selected' : '' }}>Equity</option>
                                <option value="Income" {{ old('type') == 'Income' ? 'selected' : '' }}>Income</option>
                                <option value="Expense" {{ old('type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold d-block">This account is a:</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="is_group" id="ledger" value="0" {{ old('is_group', '0') == '0' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="ledger"><i class="fas fa-file-invoice me-1"></i> Ledger (Can post DC/CR)</label>
                                
                                <input type="radio" class="btn-check" name="is_group" id="group" value="1" {{ old('is_group') == '1' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="group"><i class="fas fa-folder me-1"></i> Group (Parent of others)</label>
                            </div>
                        </div>

                        <div class="col-md-6" id="balance_container">
                            <label class="form-label text-muted small font-weight-bold">Opening Balance</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                <input type="number" step="0.01" name="opening_balance" class="form-control bg-light @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', 0) }}">
                            </div>
                            @error('opening_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                            <i class="fas fa-save me-1"></i>Save Account
                        </button>
                        <a href="{{ route('accounts.index') }}" class="btn btn-light px-4 rounded-pill">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateAccountType() {
        const parentSelect = document.getElementById('parent_id');
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const typeSelect = document.getElementById('type');
        const typeContainer = document.getElementById('type_container');

        if (selectedOption.value) {
            const parentType = selectedOption.getAttribute('data-type');
            typeSelect.value = parentType;
            typeSelect.disabled = true;
            // Add hidden input to maintain value if disabled
            if (!document.getElementById('hidden_type')) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'type';
                hidden.id = 'hidden_type';
                parentSelect.form.appendChild(hidden);
            }
            document.getElementById('hidden_type').value = parentType;
        } else {
            typeSelect.disabled = false;
            const hidden = document.getElementById('hidden_type');
            if (hidden) hidden.remove();
        }
    }

    // Preset button handler
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const code = this.getAttribute('data-code');
            const type = this.getAttribute('data-type');

            document.getElementById('name').value = name;
            document.getElementById('code').value = code;
            document.getElementById('parent_id').value = ""; // Root level
            document.getElementById('type').value = type;
            document.getElementById('group').checked = true; // Standards are usually groups
            
            // Trigger group toggle logic
            const event = new Event('change');
            document.getElementById('group').dispatchEvent(event);
            updateAccountType();
        });
    });

    // Toggle balance visibility based on group vs ledger
    document.querySelectorAll('input[name="is_group"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const balanceContainer = document.getElementById('balance_container');
            if (this.value == '1') {
                balanceContainer.style.opacity = '0.5';
                balanceContainer.querySelector('input').value = 0;
                balanceContainer.querySelector('input').readOnly = true;
            } else {
                balanceContainer.style.opacity = '1';
                balanceContainer.querySelector('input').readOnly = false;
            }
        });
    });

    window.onload = function() {
        updateAccountType();
        // Initialize state
        const checkedIsGroup = document.querySelector('input[name="is_group"]:checked').value;
        if (checkedIsGroup == '1') {
            document.getElementById('balance_container').querySelector('input').readOnly = true;
        }
    };
</script>
@endpush

@endsection

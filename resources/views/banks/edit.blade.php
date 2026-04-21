@extends('layouts.app')

@section('title', 'Edit Bank')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning-soft text-warning p-2 rounded-3 me-3">
                            <i class="fas fa-edit fa-lg"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Edit Bank</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('banks.update', $bank) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bank->name) }}" placeholder="e.g. HBL Bank" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Number</label>
                                <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $bank->account_number) }}" placeholder="Enter account number">
                                @error('account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch Name</label>
                                <input type="text" name="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch', $bank->branch) }}" placeholder="Enter branch name">
                                @error('branch') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ old('status', $bank->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $bank->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('banks.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update Bank</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
</style>
@endsection

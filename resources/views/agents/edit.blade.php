@extends('layouts.app')

@section('title', 'Edit Agent')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Edit Agent: {{ $agent->name }}</h5>
                <small class="text-muted float-end">Master Setup</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('agents.update', $agent) }}">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label fw-bold">Agent Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $agent->name) }}" required autofocus>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $agent->email) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $agent->address) }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="commission_percentage" class="form-label fw-bold">Default Commission % <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary fw-bold">%</span>
                                <input type="number" step="0.01" class="form-control" id="commission_percentage" name="commission_percentage" value="{{ old('commission_percentage', $agent->commission_percentage) }}" min="0" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Account Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" {{ $agent->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $agent->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Update Agent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

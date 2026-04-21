@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<h1><i class="fas fa-user-circle me-2"></i>My Profile</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase">Name</label>
                    <div class="form-control-plaintext fw-bold">{{ $user->name }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase">Email Address</label>
                    <div class="form-control-plaintext fw-bold">{{ $user->email }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small text-uppercase">Role</label>
                    <div>
                        <span class="badge {{ $user->role === 'Admin' ? 'bg-danger' : 'bg-primary' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-key me-2 text-warning"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

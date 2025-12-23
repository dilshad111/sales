@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<h1><i class="fas fa-edit me-2"></i>Edit User</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user me-1"></i>Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter full name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter email address">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label"><i class="fas fa-key me-1"></i>Password <small class="text-muted text-capitalize">(leave blank to keep current)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimum 8 characters">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label"><i class="fas fa-shield-alt me-1"></i>Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat password">
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label"><i class="fas fa-user-tag me-1"></i>Role</label>
                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="User" {{ old('role', $user->role) == 'User' ? 'selected' : '' }}>User</option>
                        <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Agent" {{ old('role', $user->role) == 'Agent' ? 'selected' : '' }}>Agent (for Commission)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <label class="form-label mb-0 fw-bold text-dark"><i class="fas fa-user-shield me-1 text-primary"></i>Menu Permissions</label>
                    <small class="text-muted bg-light px-2 py-1 rounded">Leave all unchecked for full access</small>
                </div>
                @error('menu_permissions')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                <div class="row g-3">
                    @foreach($menuGroups as $groupName => $groupMenus)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-light">
                                <div class="card-header bg-light py-2">
                                    <h6 class="text-uppercase text-primary small mb-0 fw-bold">{{ $groupName }}</h6>
                                </div>
                                <div class="card-body p-3">
                                    @foreach($groupMenus as $menuKey)
                                        @php($menu = $menus[$menuKey])
                                        @php($checkedPermissions = old('menu_permissions', $user->menu_permissions ?? []))
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="menu_permissions[]" id="menu_{{ $menuKey }}" value="{{ $menuKey }}" {{ in_array($menuKey, $checkedPermissions ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex align-items-center" for="menu_{{ $menuKey }}">
                                                <i class="{{ $menu['icon'] }} me-2 text-muted small" style="width: 18px;"></i>
                                                {{ $menu['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<h1><i class="fas fa-user me-2"></i>User Details</h1>

<div class="card">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <h5>User Information</h5>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Created:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Last Updated:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h5>Menu Permissions</h5>
                @php($permissions = $user->menu_permissions ?? [])
                @if(empty($permissions))
                    <div class="alert alert-success mb-0"><i class="fas fa-unlock me-1"></i>Full access granted.</div>
                @else
                    <div class="row g-2">
                        @foreach($menuGroups as $groupName => $groupMenus)
                            <div class="col-md-6">
                                <div class="border rounded p-2 h-100">
                                    <h6 class="small text-uppercase text-muted">{{ $groupName }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($groupMenus as $menuKey)
                                            @if(in_array($menuKey, $permissions, true))
                                                @php($menu = $menus[$menuKey])
                                                <li class="d-flex align-items-center">
                                                    <i class="{{ $menu['icon'] }} me-2 text-primary"></i>
                                                    <span>{{ $menu['label'] }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
        <i class="fas fa-edit me-1"></i>Edit User
    </a>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Users
    </a>
</div>
@endsection

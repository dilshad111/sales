@extends('layouts.app')

@section('title', 'User Rights Assignment')

@section('content')
<h1><i class="fas fa-user-shield me-2"></i>User Rights Assignment</h1>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Select User</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($users as $user)
                    <a href="{{ route('users.rights', ['user_id' => $user->id]) }}" 
                       class="list-group-item list-group-item-action {{ (isset($selectedUser) && $selectedUser->id == $user->id) ? 'active' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <i class="fas fa-chevron-right small"></i>
                        </div>
                        <small class="{{ (isset($selectedUser) && $selectedUser->id == $user->id) ? 'text-white-50' : 'text-muted' }}">
                            {{ $user->email }}
                        </small>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if($selectedUser)
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Permissions for: <strong>{{ $selectedUser->name }}</strong></h5>
                <span class="badge bg-info text-dark">ID: {{ $selectedUser->id }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update_rights', $selectedUser) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        @foreach($menuGroups as $groupName => $groupMenus)
                        <div class="col-12 mb-4">
                            <h6 class="bg-light p-2 fw-bold text-dark border-start border-4 border-primary shadow-sm mb-3">
                                <i class="fas fa-folder-open me-2 text-primary"></i>{{ $groupName }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle border">
                                    <thead class="bg-light small text-uppercase fw-bold text-muted">
                                        <tr>
                                            <th class="ps-3" style="width: 40%;">Module / Section</th>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Edit</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupMenus as $menuKey)
                                            @php $menu = $menus[$menuKey] ?? null; @endphp
                                            @if($menu)
                                            <tr>
                                                <td class="ps-3 fw-semibold">
                                                    <i class="{{ $menu['icon'] }} {{ $menu['color'] }} me-2 opacity-75" style="width: 20px;"></i>
                                                    {{ $menu['label'] }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="menu_permissions[{{ $menuKey }}][view]" value="1" 
                                                            {{ $selectedUser->hasMenuPermission($menuKey, 'view') ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="menu_permissions[{{ $menuKey }}][edit]" value="1" 
                                                            {{ $selectedUser->hasMenuPermission($menuKey, 'edit') ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="menu_permissions[{{ $menuKey }}][delete]" value="1" 
                                                            {{ $selectedUser->hasMenuPermission($menuKey, 'delete') ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Update Rights
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted py-5">
                <i class="fas fa-user-lock fa-4x mb-3 opacity-25"></i>
                <h5>Select a user to assign rights</h5>
                <p>Choose a user from the left panel to begin managing their menu permissions.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

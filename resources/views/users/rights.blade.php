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
                        <div class="col-md-6 mb-4">
                            <h6 class="border-bottom pb-2 mb-3 fw-bold text-primary">
                                <i class="fas fa-folder-open me-2"></i>{{ $groupName }}
                            </h6>
                            <div class="ps-2">
                                @foreach($groupMenus as $menuKey)
                                    @php $menu = $menus[$menuKey] ?? null; @endphp
                                    @if($menu)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="menu_permissions[]" 
                                               value="{{ $menuKey }}" id="perm_{{ $menuKey }}"
                                               {{ $selectedUser->hasMenuPermission($menuKey) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="perm_{{ $menuKey }}">
                                            <i class="{{ $menu['icon'] }} me-2 text-muted" style="width: 20px;"></i>
                                            {{ $menu['label'] }}
                                        </label>
                                    </div>
                                    @endif
                                @endforeach
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

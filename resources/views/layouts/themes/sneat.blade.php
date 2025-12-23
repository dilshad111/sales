<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ $companySetting->name }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bs-primary: #696cff;
            --bs-primary-rgb: 105, 108, 255;
            --bs-body-bg: #f5f5f9;
            --bs-card-cap-bg: #fff;
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }

        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bs-body-bg);
            color: #566a7f;
            overflow-x: hidden;
        }

        /* Layout Structure */
        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .layout-menu {
            width: var(--sidebar-width);
            background: #fff;
            box-shadow: 0 0.125rem 0.375rem 0 rgba(161, 172, 184, 0.12);
            z-index: 1000;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .menu-inner {
            padding: 0.5rem 0;
            display: flex;
            flex-direction: column;
        }

        .app-brand {
            display: flex;
            align-items: center;
            padding: 1.25rem 1.5rem;
            min-height: 64px;
        }

        .app-brand-text {
            font-size: 1.375rem;
            font-weight: 700;
            color: #566a7f;
            text-transform: capitalize;
            margin-left: 0.75rem;
        }

        .menu-item {
            margin: 0.2rem 1rem;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 0.625rem 1rem;
            color: #697a8d;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .menu-link:hover {
            background-color: rgba(67, 89, 113, 0.04);
            color: #566a7f;
        }

        .menu-item.active > .menu-link {
            background-color: rgba(105, 108, 255, 0.08);
            color: #696cff;
            font-weight: 500;
        }

        .menu-icon {
            margin-right: 0.75rem;
            font-size: 1.15rem;
            width: 20px;
            text-align: center;
        }

        .menu-header {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #a1acb8;
            text-transform: uppercase;
        }

        /* Page Content */
        .layout-page {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .layout-navbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 0.125rem 0.375rem 0 rgba(161, 172, 184, 0.12);
            margin: 0 1.5rem;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        .content-wrapper {
            padding: 1.5rem;
            flex: 1;
        }

        /* Card Customization */
        .card {
            border: none;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            border-radius: 0.5rem;
        }

        .card-header {
            background: transparent;
            padding: 1.5rem;
            border-bottom: none;
        }

        /* Compact Pagination & Navigation */
        .pagination {
            margin-bottom: 0;
            gap: 2px;
        }
        .pagination .page-item .page-link {
            border: none;
            border-radius: 0.375rem !important;
            padding: 0.45rem 0.85rem;
            color: #697a8d;
            font-size: 0.875rem;
            min-width: 38px;
            text-align: center;
            background-color: #f0f2f4;
            margin: 0 2px;
        }
        .pagination .page-item.active .page-link {
            background-color: #696cff;
            color: #fff;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
        }
        .pagination .page-item.disabled .page-link {
            background-color: #fcfcfd;
            color: #c7cfd6;
        }
        .pagination .page-link:focus {
            box-shadow: none;
        }

        /* SVG icon sizing in pagination */
        .pagination svg {
            width: 1.2rem;
            height: 1.2rem;
            vertical-align: middle;
        }

        /* Compact Action Buttons */
        .btn-icon {
            padding: 0;
            width: 2.125rem;
            height: 2.125rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-sm.btn-icon {
            width: 1.875rem;
            height: 1.875rem;
        }
            border-bottom: none;
        }

        .btn-primary {
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
        }

        .btn-primary:hover {
            background-color: #5f61e6;
            border-color: #5f61e6;
            transform: translateY(-1px);
        }

        /* Alerts */
        .alert-success {
            background-color: #e8fadf;
            border-color: #e8fadf;
            color: #71dd37;
        }

        /* Profile Dropdown */
        .nav-item-profile .nav-link {
            padding: 0;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #696cff;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
        }

        /* Label Utility Classes (Sneat Style) */
        .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
        .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
        .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
        .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
        .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
        .bg-label-info { background-color: #e1f0ff !important; color: #03c3ec !important; }
        .bg-label-dark { background-color: #dcdfe1 !important; color: #233446 !important; }

        @media (max-width: 991.98px) {
            .layout-menu {
                transform: translateX(-100%);
            }
            .layout-page {
                margin-left: 0;
            }
            .layout-menu.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <!-- Sidebar Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="{{ route('dashboard') }}" class="app-brand-link text-decoration-none">
                    <span class="app-brand-logo demo">
                        <i class="fas fa-cube text-primary fa-2x"></i>
                    </span>
                    <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ $companySetting->name }}</span>
                </a>
            </div>

            <ul class="menu-inner py-1">
                @auth
                @php
                    $menuConfig = config('menu_permissions');
                    $menus = $menuConfig['menus'] ?? [];
                    $groups = $menuConfig['form_groups'] ?? [];
                @endphp

                @foreach($groups as $groupName => $menuKeys)
                    @php
                        // Check if user can access at least one menu in this group
                        $visibleMenus = array_filter($menuKeys, function($key) use ($menus) {
                            return auth()->user()->hasMenuPermission($key) && isset($menus[$key]);
                        });
                    @endphp

                    @if(!empty($visibleMenus))
                        @if($loop->index > 0)
                            <li class="menu-header small text-uppercase">
                                <span class="menu-header-text">{{ $groupName }}</span>
                            </li>
                        @endif

                        @foreach($visibleMenus as $menuKey)
                            @php $menu = $menus[$menuKey]; @endphp
                            <li class="menu-item {{ request()->routeIs($menu['route'] . '*') || (isset($menu['active_on']) && request()->is($menu['active_on'])) ? 'active' : '' }}">
                                <a href="{{ route($menu['route']) }}" class="menu-link">
                                    <i class="menu-icon tf-icons {{ $menu['icon'] }} {{ $menu['color'] ?? '' }}"></i>
                                    <div>{{ $menu['label'] }}</div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
                
                <li class="menu-item mt-auto">
                    <a href="#" class="menu-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="menu-icon tf-icons fas fa-sign-out-alt"></i>
                        <div>Logout</div>
                    </a>
                </li>
                @else
                <li class="menu-item">
                    <a href="{{ route('login') }}" class="menu-link">
                        <i class="menu-icon tf-icons fas fa-sign-in-alt"></i>
                        <div>Login</div>
                    </a>
                </li>
                @endauth
            </ul>
        </aside>

        <!-- Layout Page -->
        <div class="layout-page">
            <!-- Navbar -->
            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" onclick="document.getElementById('layout-menu').classList.toggle('show')">
                        <i class="fas fa-bars"></i>
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                            <i class="fas fa-search fs-4 lh-0 text-muted me-2"></i>
                            <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search...">
                        </div>
                    </div>

                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        @auth
                        <li class="nav-item dropdown me-2">
                            <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-palette me-1"></i> Theme
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="{{ route('theme.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="theme" value="sneat">
                                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                            Sneat Theme @if(auth()->user()->theme == 'sneat') <i class="fas fa-check text-success ms-2"></i> @endif
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('theme.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="theme" value="soft-ui">
                                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                            Soft UI @if(auth()->user()->theme == 'soft-ui') <i class="fas fa-check text-success ms-2"></i> @endif
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endauth
                        <li class="nav-item me-3">
                            <button id="theme-toggle" class="btn btn-sm btn-icon border-0 shadow-none">
                                <i class="fas fa-moon fs-4 text-muted"></i>
                            </button>
                        </li>
                        @auth
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-3 text-secondary"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <span class="d-flex align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-semibold d-block text-capitalize">{{ auth()->user()->name }}</span>
                                                <small class="text-muted">{{ auth()->user()->role ?? 'User' }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-power-off me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </nav>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');

        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.setAttribute('data-bs-theme', 'dark');
            icon.className = 'fas fa-sun fs-4 text-warning';
        }

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            if (currentTheme === 'dark') {
                html.removeAttribute('data-bs-theme');
                localStorage.setItem('theme', 'light');
                icon.className = 'fas fa-moon fs-4 text-muted';
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                icon.className = 'fas fa-sun fs-4 text-warning';
            }
        });
    </script>
</body>
</html>

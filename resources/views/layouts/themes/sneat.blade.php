<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ $companySetting->name }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Local Assets for Offline Support -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
    <!-- Fallback for Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            transition: all 0.2s ease-in-out;
        }

        .layout-menu.collapsed {
            width: 80px;
        }

        .layout-menu.collapsed:hover {
            width: var(--sidebar-width);
        }

        .layout-menu.collapsed .app-brand-text,
        .layout-menu.collapsed .menu-header,
        .layout-menu.collapsed .menu-link div {
            display: none;
        }

        .layout-menu.collapsed:hover .app-brand-text,
        .layout-menu.collapsed:hover .menu-header,
        .layout-menu.collapsed:hover .menu-link div {
            display: block;
        }

        .layout-menu.collapsed:hover .menu-icon {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .layout-menu.collapsed .menu-icon {
            margin-right: 0;
            font-size: 1.25rem;
        }

        .layout-menu.collapsed .menu-item {
            margin: 0.2rem 0.5rem;
        }

        .layout-menu.collapsed .menu-link {
            justify-content: center;
            padding: 0.625rem 0;
        }

        .layout-menu.collapsed:hover .app-brand {
            justify-content: flex-start;
            padding: 1.25rem 1.5rem;
        }

        .layout-menu.collapsed:hover .menu-link {
            justify-content: flex-start;
            padding: 0.625rem 1rem;
        }

        .layout-menu.collapsed:hover .menu-item {
            margin: 0.15rem 0.75rem;
        }

        /* Hide Bootstrap tooltips when sidebar is expanded (labels are visible) */
        .layout-menu:not(.collapsed) .menu-link {
            pointer-events: auto;
        }
        /* When NOT collapsed, suppress tooltip */
        .layout-menu:not(.collapsed) [data-bs-toggle="tooltip"] {
            pointer-events: auto;
        }
        body:not(.sidebar-collapsed) .menu-link-tooltip .tooltip {
            display: none !important;
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
            margin: 0.15rem 0.75rem;
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

        /* Dark Sidebar Styles */
        .layout-menu {
            background: #232333 !important;
            box-shadow: none !important;
        }

        .app-brand-text {
            color: #dbdade !important;
        }

        .menu-link {
            color: #a3a4cc !important;
        }

        .menu-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        .menu-item.active > .menu-link {
            background-color: rgba(105, 108, 255, 0.16) !important;
            color: #7d80ff !important;
        }

        .menu-header {
            color: #5e6692 !important;
        }

        .menu-toggle::after {
            color: #5e6692 !important;
        }

        .layout-menu::-webkit-scrollbar-thumb {
            background: #444564 !important;
        }

        .menu-icon {
            margin-right: 0.65rem;
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Submenu Styling */
        .menu-sub {
            padding: 0;
            margin: 0;
            list-style: none;
            overflow: hidden;
            display: none;
            transition: all 0.3s ease;
            background: rgba(0, 0, 0, 0.02);
            border-radius: 0.375rem;
            margin-left: 0.5rem;
        }

        .menu-sub.open {
            display: block;
        }

        .menu-sub .menu-item {
            margin: 0.1rem 0.5rem;
        }

        .menu-sub .menu-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .menu-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .menu-toggle::after {
            content: "\f105";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
            margin-left: auto;
        }

        .menu-item.open > .menu-link::after {
            transform: rotate(90deg);
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
            transition: margin-left 0.2s ease-in-out;
        }

        .layout-menu.collapsed + .layout-page {
            margin-left: 80px;
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

        /* Custom Scrollbar */
        .layout-menu::-webkit-scrollbar {
            width: 5px;
        }

        .layout-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .layout-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .layout-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

        .hover-up { transition: all 0.25s ease; cursor: pointer; }
        .hover-up:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
        .ls-1 { letter-spacing: 1px; }
        .text-orange { color: #fd7e14 !important; }

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

        @media print {
            .layout-menu, 
            .layout-navbar, 
            .layout-menu-toggle,
            .btn,
            .d-print-none,
            footer {
                display: none !important;
            }
            body, 
            .layout-wrapper, 
            .layout-page, 
            .content-wrapper, 
            .container-xxl {
                background: #ffffff !important;
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                background: #ffffff !important;
            }
        }

        /* Fixed Footer Styles */
        .content-footer {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 1000;
            background: #fff;
            transition: left 0.2s ease-in-out;
        }

        .layout-menu.collapsed + .layout-page .content-footer {
            left: 80px;
        }

        .content-wrapper {
            padding-bottom: 70px; /* Space for fixed footer */
        }

        @media (max-width: 991.98px) {
            .content-footer {
                left: 0 !important;
            }
        }
        /* High-End Enterprise Table Styles */
        .table {
            color: #566a7f;
        }
        .table:not(.table-borderless) thead th {
            background-color: #f5f5f9;
            color: #566a7f;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 0.85rem 1.25rem;
            border-top: none;
            border-bottom: 2px solid #d9dee3;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 0.75rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #d9dee3;
            font-size: 0.85rem;
            color: #566a7f;
        }
        .table tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.03) !important;
        }
        .table-professional {
            border: 1px solid #d9dee3;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .table-sm thead th {
            padding: 0.5rem 0.75rem;
        }
        .table-sm tbody td {
            padding: 0.5rem 0.75rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout-wrapper">
        <!-- Sidebar Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="{{ route('dashboard') }}" class="app-brand-link text-decoration-none">
                    <span class="app-brand-logo demo">
                        <i class="fas fa-cube text-primary fa-lg"></i>
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 1.15rem;">{{ $companySetting->name }}</span>
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
                        $visibleMenus = array_filter($menuKeys, function($key) use ($menus) {
                            return auth()->user()->hasMenuPermission($key) && isset($menus[$key]);
                        });
                        $isActiveGroup = false;
                        foreach($visibleMenus as $key) {
                            if (isset($menus[$key]) && (request()->routeIs($menus[$key]['route'] . '*') || (isset($menus[$key]['active_on']) && request()->is($menus[$key]['active_on'])))) {
                                $isActiveGroup = true; break;
                            }
                        }
                    @endphp

                    @if(!empty($visibleMenus))
                        @if($groupName == 'General')
                            @foreach($visibleMenus as $menuKey)
                                @php $menu = $menus[$menuKey]; @endphp
                                <li class="menu-item {{ request()->routeIs($menu['route'] . '*') || (isset($menu['active_on']) && request()->is($menu['active_on'])) ? 'active' : '' }}">
                                    <a href="{{ route($menu['route'], $menu['params'] ?? []) }}" class="menu-link"
                                       data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $menu['label'] }}">
                                        <i class="menu-icon tf-icons {{ $menu['icon'] }} {{ $menu['color'] ?? '' }}"></i>
                                        <div>{{ $menu['label'] }}</div>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="menu-item {{ $isActiveGroup ? 'open' : '' }}">
                                <a href="javascript:void(0);" class="menu-link menu-toggle"
                                   data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $groupName }}">
                                    <i class="menu-icon tf-icons fas {{ $groupName == 'Sales' ? 'fa-cart-plus text-info' : ($groupName == 'Finance' ? 'fa-wallet text-warning' : ($groupName == 'Costing' ? 'fa-calculator text-orange' : ($groupName == 'Reports' ? 'fa-chart-pie text-success' : ($groupName == 'Purchases' ? 'fa-shopping-cart text-danger' : 'fa-gear text-info')))) }}"></i>
                                    <div>{{ $groupName }}</div>
                                </a>
                                <ul class="menu-sub {{ $isActiveGroup ? 'open' : '' }}">
                                    @foreach($visibleMenus as $menuKey)
                                        @php $menu = $menus[$menuKey]; @endphp
                                        <li class="menu-item {{ (request()->routeIs($menu['route'] . '*') && collect($menu['params'] ?? [])->every(fn($v, $k) => request()->query($k) == $v || request()->route($k) == $v)) || (isset($menu['active_on']) && request()->is($menu['active_on'])) ? 'active' : '' }}">
                                            <a href="{{ route($menu['route'], $menu['params'] ?? []) }}" class="menu-link ps-4">
                                                <i class="menu-icon tf-icons {{ $menu['icon'] }} {{ $menu['color'] ?? '' }} ms-2" style="font-size: 0.9rem;"></i>
                                                <div>{{ $menu['label'] }}</div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif
                @endforeach
                
                <li class="menu-item mt-auto">
                    <a href="#" class="menu-link text-danger"
                       data-bs-toggle="tooltip" data-bs-placement="right" title="Logout"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                
                <div class="layout-menu-toggle navbar-nav align-items-center me-3 d-none d-xl-flex">
                    <a class="nav-item nav-link px-0" href="javascript:void(0)" id="sidebar-toggle-btn">
                        <i class="fas fa-bars"></i>
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                    <div class="navbar-nav align-items-center">
                         <div class="nav-item lh-1 d-none d-md-block">
                             <div class="fw-bold text-primary small text-uppercase ls-1">{{ $companySetting->name }}</div>
                             <small class="text-muted" style="font-size: 0.65rem;">Enterprise Resource Planning</small>
                         </div>
                    </div>
                    
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        @auth
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center bg-light rounded-pill px-3 py-1 shadow-sm border" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <div class="avatar avatar-online me-2" style="width: 30px; height: 30px; min-width: 30px;">
                                     <span class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle h-100 w-100 fw-bold" style="font-size: 0.8rem;">
                                         {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                     </span>
                                </div>
                                <div class="d-none d-md-block text-start lh-1">
                                    <div class="fw-semibold text-dark small" style="font-size: 0.85rem;">{{ auth()->user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">{{ auth()->user()->role ?? 'Admin' }}</div>
                                </div>
                                <i class="fas fa-chevron-down small text-muted ms-2 d-none d-md-block" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li>
                                    <a class="dropdown-item py-2" href="#">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <span class="d-flex align-items-center justify-content-center bg-label-primary rounded-circle" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-user-shield"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-semibold d-block text-capitalize">{{ auth()->user()->name }}</span>
                                                <small class="text-muted">{{ auth()->user()->email }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li>
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

                <!-- Footer -->
                <footer class="content-footer footer bg-white py-3 border-top mt-auto shadow-sm">
                    <div class="container-xxl d-flex flex-wrap justify-content-between align-items-center">
                        <div class="mb-2 mb-md-0 text-muted" style="font-family: 'Outfit', sans-serif; font-size: 0.85rem; letter-spacing: 0.2px;">
                            Software Developed by <span class="fw-bold text-primary">SACHAAN TECHSOL</span> &copy; {{ date('Y') }}
                        </div>
                        <div class="text-muted" style="font-family: 'Outfit', sans-serif; font-size: 0.85rem;">
                            Contact / WhatsApp: <a href="https://wa.me/923002566358" target="_blank" class="footer-link fw-bold text-dark text-decoration-none">03002566358</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Local Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        // ----- Theme configuration -----
        const html = document.documentElement;
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.setAttribute('data-bs-theme', 'dark');
        }

        // ----- Sidebar Collapse + Tooltip Logic -----
        const sidebar = document.getElementById('layout-menu');
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

        const enableTooltips = () => {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                const existing = bootstrap.Tooltip.getInstance(el);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(el, { trigger: 'hover', boundary: 'window' });
            });
        };

        const disableTooltips = () => {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                const existing = bootstrap.Tooltip.getInstance(el);
                if (existing) existing.dispose();
            });
        };

        const syncTooltips = () => {
            if (sidebar.classList.contains('collapsed')) {
                enableTooltips();
            } else {
                disableTooltips();
            }
        };

        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }

        syncTooltips();

        sidebarToggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            // Small delay to let CSS transition finish before repositioning
            setTimeout(syncTooltips, 220);
        });

        // ----- Sidebar Hover Extension -----
        sidebar.addEventListener('mouseenter', () => {
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.add('hovering');
                disableTooltips();
            }
        });

        sidebar.addEventListener('mouseleave', () => {
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('hovering');
                syncTooltips();
            }
        });

        // ----- Submenu Toggle Logic -----
        document.querySelectorAll('.menu-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const menuItem = toggle.parentElement;
                const submenu = menuItem.querySelector('.menu-sub');
                
                // Close other open submenus at the same level if you want
                // document.querySelectorAll('.menu-item.open').forEach(openItem => {
                //    if (openItem !== menuItem) {
                //        openItem.classList.remove('open');
                //        openItem.querySelector('.menu-sub').classList.remove('open');
                //    }
                // });

                menuItem.classList.toggle('open');
                if (submenu) {
                    submenu.classList.toggle('open');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

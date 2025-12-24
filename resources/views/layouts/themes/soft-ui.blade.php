<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ $companySetting->name }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bs-primary: #cb0c9f;
            --bs-secondary: #8392ab;
            --bs-success: #82d616;
            --bs-info: #17adee;
            --bs-warning: #fbcf33;
            --bs-danger: #ea0606;
            --bs-dark: #344767;
            --sidebar-width: 250px;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f8f9fa;
            color: #67748e;
            overflow-x: hidden;
        }

        /* Soft UI Glassmorphism & Shadow */
        .card {
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn {
            border-radius: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.2s ease;
        }

        .bg-gradient-primary, .btn-primary {
            background-image: linear-gradient(310deg, #7928ca 0%, #cb0c9f 100%) !important;
            border: none;
        }
        .bg-gradient-dark {
            background-image: linear-gradient(310deg, #141727 0%, #3a416b 100%) !important;
        }
        .icon-sm {
            font-size: 0.875rem !important;
        }

        /* Sidebar */
        .sidenav {
            width: var(--sidebar-width);
            background: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            padding: 1rem;
            margin: 1rem;
            border-radius: 1rem;
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            overflow-y: auto;
        }

        .sidenav.collapsed {
            width: 80px;
        }

        .sidenav.collapsed .sidenav-brand-text,
        .sidenav.collapsed .nav-link-text,
        .sidenav.collapsed .menu-title,
        .sidenav.collapsed hr.horizontal {
            display: none !important;
        }

        .sidenav.collapsed .nav-link {
            justify-content: center;
            padding: 0.675rem 0;
        }

        .sidenav.collapsed .nav-link-icon {
            margin-right: 0;
        }

        .sidenav.collapsed .sidenav-header {
            padding: 1.5rem 0;
        }

        .sidenav-header {
            padding: 1.5rem;
            text-align: center;
        }

        .sidenav-brand-text {
            font-weight: 700;
            color: #344767;
            font-size: 1.1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.675rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 0.5rem;
            color: #67748e;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: #f8f9fa;
            color: #344767;
        }

        .nav-item.active .nav-link {
            background-color: #fff;
            box-shadow: 0 20px 27px 0 rgba(0,0,0,0.05);
            color: #344767;
        }

        .nav-link-icon {
            width: 32px;
            height: 32px;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: #344767;
            transition: all 0.2s ease;
        }

        .nav-item.active .nav-link-icon {
            background-image: linear-gradient(310deg, #7928ca 0%, #cb0c9f 100%);
            color: #fff;
        }

        .menu-title {
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #8392ab;
            opacity: 0.6;
        }

        /* Main Content */
        .main-content {
            margin-left: calc(var(--sidebar-width) + 2rem);
            padding: 1.5rem;
            transition: all 0.2s ease-in-out;
        }

        .sidenav.collapsed + .main-content {
            margin-left: calc(80px + 2rem);
        }

        .navbar-main {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: saturate(200%) blur(30px);
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
            padding: 0.5rem 1rem;
        }

        /* Avatar */
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
        }

        /* Badge */
        .badge {
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.45em 0.75em;
        }

        /* Table */
        .table thead th {
            padding: 0.75rem 1.5rem;
            text-transform: uppercase;
            font-size: 0.65rem;
            font-weight: 700;
            color: #8392ab;
            border-bottom: none;
        }

        .table tbody td {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
        }

        /* Label Utility Classes (Soft UI style) */
        .bg-label-primary { background-color: #f5d1e8 !important; color: #cb0c9f !important; }
        .bg-label-secondary { background-color: #e9ecef !important; color: #8392ab !important; }
        .bg-label-success { background-color: #e2f5d3 !important; color: #82d616 !important; }
        .bg-label-danger { background-color: #fddde1 !important; color: #ea0606 !important; }
        .bg-label-warning { background-color: #fff5d9 !important; color: #fbcf33 !important; }
        .bg-label-info { background-color: #d7f2fb !important; color: #17adee !important; }
        .bg-label-dark { background-color: #e2e7f0 !important; color: #344767 !important; }

        .btn-icon {
            padding: 0;
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }

        @media (max-width: 991.98px) {
            .sidenav {
                transform: translateX(-110%);
            }
            .sidenav.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="g-sidenav-show">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
        <div class="sidenav-header">
            <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
                <span class="ms-1 sidenav-brand-text">{{ $companySetting->name }}</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="ms-1 h-auto" id="sidenav-collapse-main" style="overflow-y: auto; overflow-x: hidden; max-height: calc(100vh - 150px);">
            <ul class="navbar-nav">
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
                    @endphp

                    @if(!empty($visibleMenus))
                        <li class="nav-item mt-3">
                            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">{{ $groupName }}</h6>
                        </li>
                        @foreach($visibleMenus as $menuKey)
                            @php $menu = $menus[$menuKey]; @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($menu['route'] . '*') ? 'active bg-white shadow-sm' : '' }}" href="{{ route($menu['route']) }}">
                                    <div class="nav-link-icon {{ request()->routeIs($menu['route'] . '*') ? 'bg-gradient-primary text-white' : 'bg-white shadow-sm' }}">
                                        <i class="{{ $menu['icon'] }} {{ request()->routeIs($menu['route'] . '*') ? 'text-white' : ($menu['color'] ?? '') }} icon-sm"></i>
                                    </div>
                                    <span class="nav-link-text ms-1">{{ $menu['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
                
                <li class="nav-item mt-auto mb-3">
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div class="nav-link-icon bg-white shadow-sm text-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <div class="nav-link-icon bg-white shadow-sm text-primary">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <span class="nav-link-text ms-1">Login</span>
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb" class="d-flex align-items-center">
                    <div class="sidenav-toggler-inner cursor-pointer me-3 d-none d-xl-block" id="sidebar-toggle-btn">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                    <h6 class="font-weight-bolder mb-0">@yield('title')</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text text-body border-0 bg-white"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control border-0 bg-white" placeholder="Type here...">
                        </div>
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        @auth
                        <li class="nav-item dropdown px-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-palette cursor-pointer"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <form action="{{ route('theme.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="theme" value="sneat">
                                        <button type="submit" class="dropdown-item border-radius-md d-flex align-items-center justify-content-between">
                                            Sneat Theme @if(auth()->user()->theme == 'sneat') <i class="fas fa-check text-success ms-2"></i> @endif
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('theme.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="theme" value="soft-ui">
                                        <button type="submit" class="dropdown-item border-radius-md d-flex align-items-center justify-content-between">
                                            Soft UI @if(auth()->user()->theme == 'soft-ui') <i class="fas fa-check text-success ms-2"></i> @endif
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endauth
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav" onclick="document.getElementById('sidenav-main').classList.toggle('show')">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown ps-3 d-flex align-items-center">
                            @auth
                            <a href="javascript:;" class="nav-link text-body p-0" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user cursor-pointer"></i>
                                <span class="d-sm-inline d-none ms-1 fw-bold">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="userMenuButton">
                                <li>
                                    <a class="dropdown-item border-radius-md" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                            @else
                            <a href="{{ route('login') }}" class="nav-link text-body p-0 font-weight-bold">
                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline d-none">Sign In</span>
                            </a>
                            @endauth
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <!-- End Navbar -->
        <div class="container-fluid py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
                    <span class="alert-icon"><i class="fas fa-check"></i></span>
                    <span class="alert-text">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Collapse Toggle
        const sidebar = document.getElementById('sidenav-main');
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const isCollapsed = localStorage.getItem('sidebar-collapsed-soft-ui') === 'true';

        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed-soft-ui', sidebar.classList.contains('collapsed'));
            });
        }
    </script>
</body>
</html>

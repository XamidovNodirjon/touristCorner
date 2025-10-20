<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideal Admin Dashboard - Boshqaruv Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #212529;
            --primary-color: #0d6efd;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            min-height: 100vh;
            padding: 20px 15px;
            z-index: 1050;
            background-color: var(--sidebar-bg);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            padding-top: 90px;
        }

        .navbar-top {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            position: fixed;
            top: 0;
            right: 0;
            z-index: 1020;
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .sidebar .nav-link {
            color: #ced4da;
            border-radius: 5px;
            margin-bottom: 8px;
            padding: 10px 15px;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .card.border-start {
            border-left: 0.25rem solid !important;
        }
    </style>
</head>
<body>

    <div class="sidebar text-white d-flex flex-column">
        <h5 class="text-center text-white mb-4 mt-2 border-bottom border-secondary pb-3">
            {{ auth()->user()->name }}
        </h5>
        
        <ul class="nav flex-column flex-grow-1">
            
            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item-header my-2 text-uppercase text-white-50 small">MODULLAR</li>

            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('admin.maps*') ? 'active' : '' }}" 
                   href="{{ route('admin.maps') }}">
                    <i class="bi bi-geo-alt-fill"></i> Harita Ma'lumotlarini o'zgartirish
                </a>
            </li>

            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('admin.events*') ? 'active' : '' }}" 
                   href="{{ route('admin.events') }}">
                    <i class="bi bi-calendar-event"></i> Tadbirlarni boshqarish
                </a>
            </li>

            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('admin.libraries*') ? 'active' : '' }}" 
                   href="{{ route('admin.libraries') }}">
                    <i class="bi bi-book"></i> Kutubxona
                </a>
            </li>
            
            <li class="nav-item-header my-2 text-uppercase text-white-50 small">SOZLAMALAR</li>

            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('admin.settings.security') ? 'active' : '' }}" 
                   href="#">
                    <i class="bi bi-shield-lock"></i> Xavfsizlik Sozlamalari
                </a>
            </li>
        </ul>
        
        <div class="mt-auto border-top border-secondary pt-3 text-center small text-white-50">
             &copy; 2025 Dashboard v1.0
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light navbar-top">
        <div class="container-fluid">
            <div class="d-flex me-auto"></div>
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i> 
                        <strong class="text-primary">{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Tizimdan chiqish
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin - UHTV')</title>
    
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link href="{{ asset('css/optimized.css') }}" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    
    <!-- Script de inicialización inmediata para modo oscuro -->
    <script>
        (function() {
            try {
                const DARK_MODE_KEY = 'uhtv-dark-mode';
                const savedTheme = localStorage.getItem(DARK_MODE_KEY);
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                const shouldBeDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
                
                if (shouldBeDark) {
                    document.documentElement.classList.add('dark');
                    if (document.body) {
                        document.body.classList.add('dark');
                    }
                    document.documentElement.style.colorScheme = 'dark';
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    if (document.body) {
                        document.body.classList.remove('dark');
                    }
                    document.documentElement.style.colorScheme = 'light';
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
                
                window.darkModeImmediateInit = true;
                window.darkModeInitialState = shouldBeDark;
                
            } catch (e) {
                console.warn('Error in immediate dark mode initialization:', e);
            }
        })();
    </script>
    
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        .admin-sidebar {
            background: linear-gradient(180deg, #4f46e5 0%, #4338ca 40%, #312e81 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            height: 100vh;
            max-height: 100vh;
            width: 260px;
            min-width: 260px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
            z-index: 1030;
            overflow-x: hidden;
        }

        .admin-sidebar .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }
        .admin-sidebar .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .admin-sidebar .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        
        .nav-link-admin {
            transition: all 0.25s ease;
            border-radius: 0.65rem;
            margin: 0.2rem 0;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .nav-link-admin:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
        }
        
        .nav-link-admin.active {
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
            font-weight: 700;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(78, 115, 223, 0.1);
            position: relative;
            z-index: 1010;
        }
        
        .stat-card-mini {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
            border-radius: 0.85rem;
            border: 1px solid rgba(78, 115, 223, 0.12);
            transition: all 0.3s ease;
        }
        
        .stat-card-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(78, 115, 223, 0.15);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.1) 100%);
            border: 2px solid rgba(255, 255, 255, 0.35);
        }

        /* Notificaciones dropdown */
        .notif-dropdown-menu {
            min-width: 330px;
            max-width: 360px;
            border-radius: 14px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 0, 0, 0.08);
            z-index: 1060;
        }

        .notif-item {
            white-space: normal;
            transition: background-color 0.2s ease;
            text-decoration: none;
        }
        .notif-item:hover {
            background-color: rgba(79, 70, 229, 0.06);
        }
        
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 1050;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
        }

        /* Soporte para Modo Oscuro en Admin */
        .dark body {
            background-color: #0b0f19 !important;
            color: #f1f5f9 !important;
        }
        .dark .admin-header {
            background: #111827 !important;
            border-bottom: 1px solid #1f2937 !important;
        }
        .dark .admin-header h2,
        .dark .admin-header .text-dark {
            color: #f9fafb !important;
        }
        .dark .admin-header .text-muted {
            color: #9ca3af !important;
        }
        .dark .stat-card-mini {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .stat-card-mini .text-muted {
            color: #9ca3af !important;
        }
        .dark .dashboard-card {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #f3f4f6 !important;
        }
        .dark .dashboard-card h2,
        .dark .dashboard-card h3,
        .dark .dashboard-card h4,
        .dark .dashboard-card h5,
        .dark .dashboard-card h6 {
            color: #f9fafb !important;
        }
        .dark .dashboard-card .text-muted {
            color: #9ca3af !important;
        }
        .dark .activity-item {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
        }
        .dark .activity-item:hover {
            background-color: #1f2937 !important;
        }
        .dark .dropdown-menu {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
            color: #f3f4f6 !important;
        }
        .dark .dropdown-item {
            color: #e5e7eb !important;
        }
        .dark .dropdown-item:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }
        .dark .dropdown-divider {
            border-color: #374151 !important;
        }
        .dark .notif-item .notif-title {
            color: #f9fafb !important;
        }
        .dark .notif-item .notif-msg {
            color: #9ca3af !important;
        }
        .dark .notif-item:hover {
            background-color: #374151 !important;
        }
        .dark .notif-footer {
            background-color: #111827 !important;
            border-color: #374151 !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-light">
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar Fijo / Sticky -->
        <aside id="sidebar" class="admin-sidebar text-white sidebar-transition">
            <!-- Logo Header -->
            <div class="px-4 py-3 border-bottom border-white border-opacity-20 flex-shrink-0">
                <div class="d-flex align-items-center">
                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-tv fa-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="h6 mb-0 fw-bold text-white tracking-wide">UHTV Admin</h1>
                        <p class="small mb-0 text-white-50" style="font-size: 0.72rem;">Panel de Control</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-fill px-3 py-3 overflow-y-auto sidebar-nav">
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt me-3" style="width: 20px;"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.noticias.index') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.noticias.*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper me-3" style="width: 20px;"></i>
                            <span>Noticias</span>
                            @if(isset($headerTotalNews) && $headerTotalNews > 0)
                                <span class="badge bg-white text-primary ms-auto fw-bold" style="font-size: 0.7rem;">{{ $headerTotalNews }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categorias.index') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
                            <i class="fas fa-tags me-3" style="width: 20px;"></i>
                            <span>Categorías</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.banners.index') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                            <i class="fas fa-images me-3" style="width: 20px;"></i>
                            <span>Banners</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.transmisiones.index') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.transmisiones.*') ? 'active' : '' }}">
                            <i class="fas fa-satellite-dish me-3" style="width: 20px;"></i>
                            <span>En Vivo / Podcasts</span>
                            @if(isset($headerLiveStreamCount) && $headerLiveStreamCount > 0)
                                <span class="badge bg-danger text-white ms-auto fw-bold animate-pulse" style="font-size: 0.65rem;">
                                    <i class="fas fa-circle fa-2xs me-1"></i>VIVO
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.profile.index') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3 {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                            <i class="fas fa-user-cog me-3" style="width: 20px;"></i>
                            <span>Mi Perfil</span>
                        </a>
                    </li>
                    <li class="mt-2 pt-2 border-top border-white border-opacity-20">
                        <a href="{{ route('portada') }}" 
                           class="nav-link-admin d-flex align-items-center text-white text-decoration-none py-2 px-3"
                           target="_blank">
                            <i class="fas fa-external-link-alt me-3" style="width: 20px;"></i>
                            <span>Ver Sitio Web</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Info & Profile Fijo en la Base del Sidebar -->
            <div class="px-3 py-3 border-top border-white border-opacity-20 mt-auto flex-shrink-0" style="background: rgba(0, 0, 0, 0.22);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <a href="{{ route('admin.profile.index') }}" class="d-flex align-items-center text-decoration-none text-white overflow-hidden me-2" title="Ir a Mi Perfil">
                        <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="text-truncate">
                            <p class="small mb-0 fw-bold text-truncate text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="mb-0 text-white-50 d-flex align-items-center" style="font-size: 0.72rem;">
                                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 7px; height: 7px;"></span>
                                {{ auth()->user()->role === 'admin' ? 'Administrador' : 'Editor' }}
                            </p>
                        </div>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-white text-opacity-75 hover-opacity-100 p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones de cuenta">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 10px; min-width: 170px;">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.profile.index') }}">
                                    <i class="fas fa-user-cog me-2 text-primary"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('portada') }}" target="_blank">
                                    <i class="fas fa-external-link-alt me-2 text-secondary"></i>Ver Portada
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Botón directo a Mi Perfil siempre visible -->
                <div class="d-grid mt-2">
                    <a href="{{ route('admin.profile.index') }}" 
                       class="btn btn-sm d-flex align-items-center justify-content-center gap-2 fw-medium text-white {{ request()->routeIs('admin.profile.*') ? 'bg-white text-dark shadow-sm' : 'border border-white border-opacity-25' }}" 
                       style="border-radius: 8px; font-size: 0.8rem; padding: 6px 12px; background: {{ request()->routeIs('admin.profile.*') ? '#ffffff' : 'rgba(255,255,255,0.12)' }};">
                        <i class="fas fa-id-badge"></i>
                        <span>Administrar Mi Perfil</span>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-grow-1 min-vw-0 d-flex flex-column" style="min-width: 0;">
            <!-- Top Bar -->
            <header class="admin-header shadow-sm p-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggle" class="btn btn-link d-lg-none text-muted p-0 me-3" aria-label="Abrir Menú">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h2 class="h4 mb-0 text-dark fw-bold">
                                @yield('page-title', 'Panel de Administración')
                            </h2>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                {{ ucfirst(now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <!-- Quick Stats -->
                        <div class="d-none d-md-flex gap-3">
                            <div class="stat-card-mini p-2 px-3 text-center" style="min-width: 85px;">
                                <div class="small text-muted" style="font-size: 0.75rem;">Noticias</div>
                                <div class="fw-bold text-primary">
                                    {{ $headerTotalNews ?? (auth()->user()->role === 'admin' ? \App\Models\Noticia::count() : 0) }}
                                </div>
                            </div>
                            <div class="stat-card-mini p-2 px-3 text-center" style="min-width: 85px;">
                                <div class="small text-muted" style="font-size: 0.75rem;">Publicadas</div>
                                <div class="fw-bold text-success">
                                    {{ $headerPublishedNews ?? (auth()->user()->role === 'admin' ? \App\Models\Noticia::where('publicada', true)->count() : 0) }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Toggle de modo oscuro -->
                        <button data-dark-mode-toggle class="btn btn-link text-muted p-2 me-1" type="button" aria-label="Cambiar modo oscuro" title="Alternar modo oscuro">
                            <i class="fas fa-sun sun-icon fa-lg hidden"></i>
                            <i class="fas fa-moon moon-icon fa-lg"></i>
                        </button>

                        <!-- Dropdown de Notificaciones Interactivo y Funcional -->
                        <div class="dropdown position-relative" id="adminNotificationsContainer">
                            <button id="adminNotificationsBtn" class="btn btn-link text-muted position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                                <i class="fas fa-bell fa-lg"></i>
                                @php
                                    $notifCount = isset($adminNotifications) ? $adminNotifications->count() : 0;
                                @endphp
                                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $notifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem; font-weight: 700; padding: 3px 6px;">
                                    {{ $notifCount }}
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notif-dropdown-menu p-0" style="z-index: 1070;">
                                <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 13px 13px 0 0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-bell"></i>
                                        <span class="fw-bold">Notificaciones</span>
                                        <span id="notifHeaderBadge" class="badge bg-white text-primary rounded-pill px-2" style="font-size: 0.7rem;">{{ $notifCount }}</span>
                                    </div>
                                    <button type="button" id="markAllReadBtn" class="btn btn-sm btn-link text-white text-decoration-none p-0" style="font-size: 0.75rem; opacity: 0.9;" title="Limpiar notificaciones">
                                        <i class="fas fa-check-double me-1"></i>Limpiar
                                    </button>
                                </div>
                                
                                <div id="notifItemsList" style="max-height: 320px; overflow-y: auto;">
                                    @if(isset($adminNotifications) && $adminNotifications->count() > 0)
                                        @foreach($adminNotifications as $notif)
                                            <a href="{{ $notif['url'] }}" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 notif-item">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                                     style="width: 36px; height: 36px; background-color: {{ $notif['type'] === 'warning' ? '#fef3c7' : ($notif['type'] === 'primary' ? '#e0e7ff' : ($notif['type'] === 'success' ? '#d1fae5' : '#e0f2fe')) }}; color: {{ $notif['type'] === 'warning' ? '#d97706' : ($notif['type'] === 'primary' ? '#4f46e5' : ($notif['type'] === 'success' ? '#059669' : '#0284c7')) }};">
                                                    <i class="{{ $notif['icon'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="fw-bold small text-dark notif-title">{{ $notif['title'] }}</span>
                                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $notif['time'] }}</span>
                                                    </div>
                                                    <p class="mb-0 text-muted small lh-sm notif-msg">{{ $notif['message'] }}</p>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-4 text-center text-muted" id="notifEmptyState">
                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                            <p class="mb-0 small fw-medium">No tienes notificaciones pendientes</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-2 border-top text-center notif-footer" style="background-color: #f8fafc; border-radius: 0 0 13px 13px;">
                                    <a href="{{ route('admin.noticias.index') }}" class="text-decoration-none small fw-bold text-primary">
                                        <i class="fas fa-newspaper me-1"></i>Ver todas las noticias
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <a href="{{ route('admin.noticias.create') }}" 
                           class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Nueva Noticia</span>
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-fill p-4">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Errores encontrados:</strong>
                        </div>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts -->
    <script>
        // Sidebar Toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('d-none');
        });
        
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.add('d-none');
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
        
        // Notificaciones Interactivas
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        const notifBadge = document.getElementById('notifBadge');
        const notifHeaderBadge = document.getElementById('notifHeaderBadge');
        const notifItemsList = document.getElementById('notifItemsList');
        const NOTIF_STORAGE_KEY = 'uhtv_admin_notifs_cleared_at';

        // Comprobar si fueron limpiadas recientemente (últimas 4 horas)
        const lastCleared = localStorage.getItem(NOTIF_STORAGE_KEY);
        if (lastCleared && (Date.now() - parseInt(lastCleared, 10)) < 4 * 3600 * 1000) {
            if (notifBadge) notifBadge.classList.add('d-none');
            if (notifHeaderBadge) notifHeaderBadge.textContent = '0';
        }

        markAllReadBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            localStorage.setItem(NOTIF_STORAGE_KEY, Date.now().toString());
            if (notifBadge) notifBadge.classList.add('d-none');
            if (notifHeaderBadge) notifHeaderBadge.textContent = '0';
            if (notifItemsList) {
                notifItemsList.innerHTML = `
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="mb-0 small fw-medium">Todas las notificaciones han sido marcadas como leídas</p>
                    </div>
                `;
            }
        });

        // Tooltips initialization
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    
    <!-- Dark Mode Script -->
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
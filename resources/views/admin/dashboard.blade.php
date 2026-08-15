@extends('layouts.admin')

@section('title', 'Dashboard - UHTV Admin')
@section('page-title', 'Panel de Administración')

@push('styles')
<style>
    .dashboard-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fc 100%);
        border-radius: 1rem;
        border: 1px solid rgba(78, 115, 223, 0.1);
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(78, 115, 223, 0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .stat-number {
        background: linear-gradient(45deg, #4e73df, #224abe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
    }
    
    .quick-action-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .quick-action-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }
    
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }
    
    .activity-item {
        transition: all 0.3s ease;
        border-radius: 0.75rem;
    }
    
    .activity-item:hover {
        background-color: #f8f9fc !important;
        transform: translateX(5px);
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
    .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
    .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
    .dashboard-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="h3 mb-2 text-primary fw-bold">
                            ¡Bienvenido de vuelta, {{ auth()->user()->name }}! 👋
                        </h2>
                        <p class="text-muted mb-0">
                            Aquí tienes un resumen de tu actividad y accesos rápidos para gestionar tu contenido.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.noticias.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Nueva Noticia
                            </a>
                            <a href="{{ route('portada') }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="fas fa-external-link-alt me-2"></i>Ver Sitio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Noticias
                        </div>
                        <div class="h4 mb-0 stat-number">{{ $stats['total_published'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Borradores
                        </div>
                        <div class="h4 mb-0 stat-number">{{ $stats['total_draft'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Categorías
                        </div>
                        <div class="h4 mb-0 stat-number">{{ $stats['total_categories'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Esta Semana
                        </div>
                        <div class="h4 mb-0 stat-number">{{ $stats['recent_news'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="quick-action-card p-4 h-100 position-relative">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-newspaper fa-2x me-3"></i>
                    <h5 class="mb-0 fw-bold">Gestión de Noticias</h5>
                </div>
                <p class="mb-4 opacity-90">Administra todas las noticias de tu sitio web de manera eficiente.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.noticias.index') }}" 
                       class="btn btn-light">
                        <i class="fas fa-list me-2"></i>Ver Todas las Noticias
                    </a>
                    <a href="{{ route('admin.noticias.create') }}" 
                       class="btn btn-outline-light">
                        <i class="fas fa-plus me-2"></i>Crear Nueva Noticia
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="quick-action-card p-4 h-100 position-relative" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-tags fa-2x me-3"></i>
                    <h5 class="mb-0 fw-bold">Gestión de Categorías</h5>
                </div>
                <p class="mb-4 opacity-90">Organiza tus noticias por categorías para mejor navegación.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.categorias.index') }}" 
                       class="btn btn-light">
                        <i class="fas fa-list me-2"></i>Ver Categorías
                    </a>
                    <a href="{{ route('admin.categorias.create') }}" 
                       class="btn btn-outline-light">
                        <i class="fas fa-plus me-2"></i>Nueva Categoría
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="quick-action-card p-4 h-100 position-relative" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-user-cog fa-2x me-3"></i>
                    <h5 class="mb-0 fw-bold">Mi Perfil</h5>
                </div>
                <p class="mb-4 opacity-90">Administra tu perfil y configuraciones personales.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.profile.index') }}" 
                       class="btn btn-light">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </a>
                    <a href="{{ route('portada') }}" 
                       target="_blank"
                       class="btn btn-outline-light">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Sitio Web
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-clock me-2"></i>Actividad Reciente
                        </h5>
                        <a href="{{ route('admin.noticias.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver Todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(isset($recentNews) && $recentNews->count() > 0)
                        <div class="row">
                            @foreach($recentNews as $noticia)
                                <div class="col-12 mb-3">
                                    <div class="activity-item d-flex align-items-center p-3 bg-light">
                                        <div class="flex-shrink-0 me-3">
                                            @if($noticia->imagen)
                                                <img src="{{ $noticia->imagenUrl }}" 
                                                     alt="{{ $noticia->titulo }}" 
                                                     class="rounded" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ Str::limit($noticia->titulo, 60) }}</h6>
                                            <p class="mb-1 text-muted small">
                                                <i class="fas fa-tag me-1"></i>{{ $noticia->category->name ?? 'Sin categoría' }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar me-1"></i>{{ $noticia->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $noticia->publicada ? 'bg-success' : 'bg-warning' }}">
                                                {{ $noticia->publicada ? 'Publicada' : 'Borrador' }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.noticias.edit', $noticia->id) }}">
                                                            <i class="fas fa-edit me-2"></i>Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('show', $noticia->id) }}" target="_blank">
                                                            <i class="fas fa-eye me-2"></i>Ver
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No hay noticias recientes</h5>
                            <p class="text-muted">Comienza creando tu primera noticia para ver la actividad aquí.</p>
                            <a href="{{ route('admin.noticias.create') }}" 
                               class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Crear Primera Noticia
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

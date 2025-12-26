@extends('layouts.admin')

@section('title', 'Gestión de Banners - UHTV Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Banners Publicitarios</h1>
            <p class="text-muted mb-0">Administra los espacios publicitarios de tu sitio web</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Banner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Banner Location Guide -->
    <div class="card shadow mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-map-marked-alt me-2"></i>Ubicaciones de Banners en el Sitio
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Header Banner -->
                <div class="col-md-4">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-image me-2"></i>Banner Principal (Header)</h6>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 120px; border-radius: 8px; overflow: hidden;">
                                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                    <div class="text-center">
                                        <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Fondo del Header</p>
                                    </div>
                                </div>
                            </div>
                            <p class="small mb-2"><strong>Ubicación:</strong> <code>portada_top</code></p>
                            <p class="small mb-2"><strong>Descripción:</strong> Imagen de fondo del encabezado principal del sitio</p>
                            <p class="small mb-0"><strong>Dimensiones recomendadas:</strong> 1920x400px</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Banner -->
                <div class="col-md-4">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-image me-2"></i>Banner Lateral (Sidebar)</h6>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); height: 120px; border-radius: 8px; overflow: hidden;">
                                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                    <div class="text-center">
                                        <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Sidebar Derecho</p>
                                    </div>
                                </div>
                            </div>
                            <p class="small mb-2"><strong>Ubicación:</strong> <code>sidebar</code></p>
                            <p class="small mb-2"><strong>Descripción:</strong> Banner publicitario en la barra lateral derecha de la portada</p>
                            <p class="small mb-0"><strong>Dimensiones recomendadas:</strong> 300x600px</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Banner -->
                <div class="col-md-4">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-image me-2"></i>Banner Inferior (Footer)</h6>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-3" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); height: 120px; border-radius: 8px; overflow: hidden;">
                                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                    <div class="text-center">
                                        <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Banner Horizontal</p>
                                    </div>
                                </div>
                            </div>
                            <p class="small mb-2"><strong>Ubicación:</strong> <code>footer</code></p>
                            <p class="small mb-2"><strong>Descripción:</strong> Banner horizontal debajo del menú de navegación</p>
                            <p class="small mb-0"><strong>Dimensiones recomendadas:</strong> 1200x200px</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banners List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Banners Activos
            </h6>
            <span class="badge bg-primary">{{ $banners->count() }} banners</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">Vista Previa</th>
                            <th>Título</th>
                            <th>Ubicación</th>
                            <th style="width: 100px;">Posición</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td>
                                    <img src="{{ asset($banner->image_path) }}" 
                                         alt="{{ $banner->title }}" 
                                         class="img-thumbnail" 
                                         style="max-height: 60px; max-width: 100px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->link)
                                        <br><small class="text-muted"><i class="fas fa-link me-1"></i>{{ Str::limit($banner->link, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $locationInfo = [
                                            'portada_top' => ['label' => 'Header Principal', 'color' => 'primary', 'icon' => 'fa-image'],
                                            'sidebar' => ['label' => 'Sidebar Derecho', 'color' => 'info', 'icon' => 'fa-sidebar'],
                                            'footer' => ['label' => 'Footer Horizontal', 'color' => 'success', 'icon' => 'fa-rectangle-ad']
                                        ];
                                        $info = $locationInfo[$banner->location] ?? ['label' => $banner->location, 'color' => 'secondary', 'icon' => 'fa-question'];
                                    @endphp
                                    <span class="badge bg-{{ $info['color'] }}">
                                        <i class="fas {{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                                    </span>
                                    <br><small class="text-muted"><code>{{ $banner->location }}</code></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $banner->position }}</span>
                                </td>
                                <td>
                                    @if($banner->active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-pause-circle me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" 
                                              method="POST" 
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar este banner?\n\nEsta acción no se puede deshacer.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-3">No hay banners registrados.</p>
                                    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Crear Primer Banner
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card-header.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}
</style>
@endsection

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
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-map-marked-alt me-2"></i>Dónde aparece cada banner en el sitio
            </h6>
            <span class="badge bg-white text-primary">
                {{ count($bannerLocations) }} espacios publicitarios
            </span>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-4">
                Cada dibujo representa la maquetación real de la página. El recuadro morado
                señala el hueco exacto donde se mostrará el banner, con las mismas proporciones
                que tendrá en producción.
            </p>

            <div class="row g-4">
                @foreach($bannerLocations as $uhtvKey)
                    <div class="col-md-6 col-xl-4">
                        @include('admin.banners.partials.location-card', ['locationKey' => $uhtvKey])

                        @if(\App\Services\BannerLocationService::publicUrl($uhtvKey))
                            <a href="{{ \App\Services\BannerLocationService::publicUrl($uhtvKey) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-external-link-alt me-1"></i>Ver la portada real
                            </a>
                        @endif
                    </div>
                @endforeach
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
                                         loading="lazy"
                                         decoding="async"
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
                                        $info = \App\Services\BannerLocationService::get($banner->location)
                                            ?? ['label' => $banner->location, 'color' => 'secondary', 'icon' => 'fa-question'];
                                    @endphp
                                    <span class="badge bg-{{ $info['color'] }}">
                                        <i class="fas {{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                                    </span>
                                    <br><small class="text-muted"><code>{{ $banner->location }}</code></small>
                                    <br><small class="text-muted"><i class="fas fa-ruler-combined me-1"></i>{{ $info['dimensions'] ?? '' }}</small>
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

@extends('layouts.admin')

@section('title', 'Transmisiones En Vivo y Podcasts - UHTV Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold d-flex align-items-center">
                <i class="fas fa-satellite-dish text-danger me-2"></i> Transmisiones En Vivo, Podcasts y Clips
            </h1>
            <p class="text-muted mb-0 small">Administra los streams en directo, episodios de podcast y videos de YouTube, Facebook, TikTok y Twitch</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-sm-0">
            <a href="{{ route('transmisiones.en-vivo') }}" target="_blank" class="btn btn-outline-danger shadow-sm">
                <i class="fas fa-external-link-alt me-1"></i> Ver Página En Vivo
            </a>
            <a href="{{ route('admin.transmisiones.create') }}" class="btn btn-danger shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> Nueva Transmisión / Video
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fa-lg me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-danger h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">En Vivo Ahora</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ $stats['en_vivo'] }}</h3>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="fas fa-broadcast-tower fa-lg {{ $stats['en_vivo'] > 0 ? 'fa-beat' : '' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-primary h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">Total Videos</p>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fas fa-film fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-purple h-100" style="border-left-color: #6f42c1 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">Podcasts</p>
                            <h3 class="mb-0 fw-bold" style="color: #6f42c1;">{{ $stats['podcasts'] }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="fas fa-podcast fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-warning h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.72rem;">Clips / Cortos</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['clips'] }}</h3>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="fas fa-bolt fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.transmisiones.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Buscar por título o descripción...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <option value="en_vivo" {{ request('tipo') == 'en_vivo' ? 'selected' : '' }}>🔴 En Vivo</option>
                        <option value="podcast" {{ request('tipo') == 'podcast' ? 'selected' : '' }}>🎙️ Podcast</option>
                        <option value="clip" {{ request('tipo') == 'clip' ? 'selected' : '' }}>⚡ Clip</option>
                        <option value="programa" {{ request('tipo') == 'programa' ? 'selected' : '' }}>📺 Programa</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="plataforma" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todas las plataformas</option>
                        <option value="youtube" {{ request('plataforma') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                        <option value="facebook" {{ request('plataforma') == 'facebook' ? 'selected' : '' }}>Facebook Live</option>
                        <option value="tiktok" {{ request('plataforma') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="twitch" {{ request('plataforma') == 'twitch' ? 'selected' : '' }}>Twitch</option>
                        <option value="otro" {{ request('plataforma') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="en_vivo" {{ request('estado') == 'en_vivo' ? 'selected' : '' }}>🔴 Transmitiendo Ahora</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['q', 'tipo', 'plataforma', 'estado']))
                        <a href="{{ route('admin.transmisiones.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Listado -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-list me-2 text-danger"></i> Registros de Transmisiones
            </h6>
            <span class="badge bg-light text-dark border">{{ $transmisiones->total() }} registros</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 90px;" class="ps-3">Video</th>
                            <th>Título y Detalles</th>
                            <th style="width: 130px;">Tipo</th>
                            <th style="width: 140px;">Plataforma</th>
                            <th style="width: 160px;" class="text-center">En Vivo Ahora</th>
                            <th style="width: 110px;" class="text-center">Estado</th>
                            <th style="width: 150px;" class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transmisiones as $item)
                            <tr class="{{ $item->en_vivo ? 'table-danger table-opacity-10' : '' }}">
                                <td class="ps-3">
                                    <div class="position-relative rounded-2 overflow-hidden shadow-sm" style="width: 72px; height: 48px; background: #000;">
                                        <img src="{{ $item->effective_thumbnail }}" alt="{{ $item->titulo }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async" onerror="this.src='/images/Logo.jpg'">
                                        @if($item->en_vivo)
                                            <span class="position-absolute top-0 start-0 badge bg-danger text-white p-1 rounded-0" style="font-size: 0.55rem;">
                                                <i class="fas fa-circle fa-fade"></i> VIVO
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        {{ $item->titulo }}
                                        @if($item->destacado)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;" title="Destacado en portada">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small text-truncate" style="max-width: 420px;">
                                        {{ $item->descripcion ?: 'Sin descripción adicional' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <i class="far fa-clock me-1"></i>{{ $item->fecha_transmision ? $item->fecha_transmision->format('d/m/Y H:i') : $item->created_at->format('d/m/Y') }}
                                        @if($item->duracion)
                                            <span class="ms-2"><i class="fas fa-hourglass-half me-1"></i>{{ $item->duracion }}</span>
                                        @endif
                                        <span class="ms-2"><i class="far fa-eye me-1"></i>{{ number_format($item->views) }} vistas</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->tipo_color }} bg-opacity-10 text-{{ $item->tipo_color }} border border-{{ $item->tipo_color }} border-opacity-25 px-2 py-1">
                                        {{ $item->tipo_nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1 px-2.5 py-1 text-white" style="background-color: {{ $item->plataforma_color }};">
                                        <i class="{{ $item->plataforma_icon }}"></i>
                                        <span>{{ $item->plataforma_nombre }}</span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.transmisiones.toggle-live', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($item->en_vivo)
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1 fw-bold shadow-sm" title="Clic para finalizar transmisión en vivo">
                                                <i class="fas fa-broadcast-tower fa-beat me-1"></i> EN VIVO
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Clic para activar como transmisión en vivo principal">
                                                <i class="far fa-circle me-1"></i> Iniciar Vivo
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.transmisiones.toggle-active', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($item->activo)
                                            <button type="submit" class="btn btn-sm btn-light text-success border px-2 py-1" title="Clic para ocultar">
                                                <i class="fas fa-check-circle me-1"></i> Activo
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-light text-muted border px-2 py-1" title="Clic para mostrar">
                                                <i class="fas fa-eye-slash me-1"></i> Oculto
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-primary preview-btn" 
                                                data-title="{{ $item->titulo }}"
                                                data-embed="{{ $item->embed_url }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#quickPreviewModal"
                                                title="Previsualizar video">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <a href="{{ route('admin.transmisiones.edit', $item->id) }}" class="btn btn-outline-secondary" title="Editar transmisión">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.transmisiones.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta transmisión?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-video-slash fa-3x mb-3 text-gray-300"></i>
                                        <p class="mb-1 fw-semibold">No se encontraron transmisiones ni videos registrados</p>
                                        <p class="small text-muted mb-3">Comienza registrando un stream en vivo, podcast o clip de video</p>
                                        <a href="{{ route('admin.transmisiones.create') }}" class="btn btn-sm btn-danger">
                                            <i class="fas fa-plus me-1"></i> Registrar Primera Transmisión
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($transmisiones->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $transmisiones->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Quick Preview -->
<div class="modal fade" id="quickPreviewModal" tabindex="-1" aria-labelledby="quickPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold text-truncate" id="quickPreviewModalLabel">
                    <i class="fas fa-play-circle text-danger me-2"></i> Vista Previa
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="ratio ratio-16x9">
                    <iframe id="previewIframe" src="" title="Video Preview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewModal = document.getElementById('quickPreviewModal');
    const previewIframe = document.getElementById('previewIframe');
    const previewTitle = document.getElementById('quickPreviewModalLabel');

    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const embed = button.getAttribute('data-embed');
            const title = button.getAttribute('data-title');

            previewTitle.innerHTML = `<i class="fas fa-play-circle text-danger me-2"></i> ${title}`;
            previewIframe.src = embed;
        });

        previewModal.addEventListener('hidden.bs.modal', function() {
            previewIframe.src = '';
        });
    }
});
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Editar Transmisión - UHTV Admin')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transmisiones.index') }}">Transmisiones</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800 fw-bold d-flex align-items-center">
                <i class="fas fa-edit text-danger me-2"></i> Editar Transmisión o Video
            </h1>
        </div>
        <a href="{{ route('admin.transmisiones.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Por favor revisa los siguientes errores:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.transmisiones.update', $transmision->id) }}" method="POST" id="transmisionForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Columna Izquierda: Datos del Formulario -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="fas fa-link text-danger me-2"></i> Enlace y Plataforma
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- URL del Video / Transmisión -->
                        <div class="mb-3">
                            <label for="urlInput" class="form-label fw-bold">URL del Video, Stream o Código de Inserción <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-globe"></i></span>
                                <input type="text" 
                                       name="url" 
                                       id="urlInput" 
                                       class="form-control @error('url') is-invalid @enderror" 
                                       placeholder="https://www.youtube.com/watch?v=... o https://fb.watch/... o TikTok / Twitch"
                                       value="{{ old('url', $transmision->url) }}" 
                                       required>
                                <button type="button" id="btnTestPreview" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Detectar y Probar
                                </button>
                            </div>
                            <div class="form-text small text-muted mt-1">
                                Admite enlaces de <strong>YouTube</strong> (en vivo, videos, shorts), <strong>Facebook</strong> (lives, videos), <strong>TikTok</strong>, <strong>Twitch</strong> o código <code>&lt;iframe&gt;</code>.
                            </div>
                            @error('url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Tipo de Contenido -->
                            <div class="col-md-6">
                                <label for="tipoSelect" class="form-label fw-bold">Tipo de Contenido <span class="text-danger">*</span></label>
                                <select name="tipo" id="tipoSelect" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="en_vivo" {{ old('tipo', $transmision->tipo) == 'en_vivo' ? 'selected' : '' }}>🔴 Transmisión En Vivo</option>
                                    <option value="podcast" {{ old('tipo', $transmision->tipo) == 'podcast' ? 'selected' : '' }}>🎙️ Podcast / Programa Radial</option>
                                    <option value="clip" {{ old('tipo', $transmision->tipo) == 'clip' ? 'selected' : '' }}>⚡ Clip / Resumen Corto</option>
                                    <option value="programa" {{ old('tipo', $transmision->tipo) == 'programa' ? 'selected' : '' }}>📺 Programa Completo Grabado</option>
                                </select>
                                @error('tipo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Plataforma -->
                            <div class="col-md-6">
                                <label for="plataformaSelect" class="form-label fw-bold">Plataforma <span class="text-danger">*</span></label>
                                <select name="plataforma" id="plataformaSelect" class="form-select @error('plataforma') is-invalid @enderror" required>
                                    <option value="youtube" {{ old('plataforma', $transmision->plataforma) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="facebook" {{ old('plataforma', $transmision->plataforma) == 'facebook' ? 'selected' : '' }}>Facebook Live / Video</option>
                                    <option value="tiktok" {{ old('plataforma', $transmision->plataforma) == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                    <option value="twitch" {{ old('plataforma', $transmision->plataforma) == 'twitch' ? 'selected' : '' }}>Twitch</option>
                                    <option value="otro" {{ old('plataforma', $transmision->plataforma) == 'otro' ? 'selected' : '' }}>Otro / Iframe Directo</option>
                                </select>
                                @error('plataforma')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="tituloInput" class="form-label fw-bold">Título de la Transmisión o Video <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="titulo" 
                                   id="tituloInput" 
                                   class="form-control @error('titulo') is-invalid @enderror" 
                                   placeholder="Ej: UHTV Noticias Edición Central - En Directo"
                                   value="{{ old('titulo', $transmision->titulo) }}" 
                                   required>
                            @error('titulo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcionInput" class="form-label fw-semibold">Descripción o Resumen (opcional)</label>
                            <textarea name="descripcion" 
                                      id="descripcionInput" 
                                      rows="3" 
                                      class="form-control @error('descripcion') is-invalid @enderror" 
                                      placeholder="Breve reseña del contenido o participantes...">{{ old('descripcion', $transmision->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <!-- Duración -->
                            <div class="col-md-6">
                                <label for="duracionInput" class="form-label fw-semibold">Duración (opcional)</label>
                                <input type="text" 
                                       name="duracion" 
                                       id="duracionInput" 
                                       class="form-control" 
                                       placeholder="Ej: 45:30 o 1h 15m" 
                                       value="{{ old('duracion', $transmision->duracion) }}">
                            </div>

                            <!-- Fecha de Transmisión -->
                            <div class="col-md-6">
                                <label for="fechaInput" class="form-label fw-semibold">Fecha y Hora de Emisión</label>
                                <input type="datetime-local" 
                                       name="fecha_transmision" 
                                       id="fechaInput" 
                                       class="form-control" 
                                       value="{{ old('fecha_transmision', $transmision->fecha_transmision ? $transmision->fecha_transmision->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>

                        <!-- Miniatura Personalizada -->
                        <div class="mt-3">
                            <label for="thumbnailInput" class="form-label fw-semibold">URL de Miniatura Personalizada (opcional)</label>
                            <input type="text" 
                                   name="thumbnail_url" 
                                   id="thumbnailInput" 
                                   class="form-control" 
                                   placeholder="https://... (para YouTube se autogenera si se deja vacío)" 
                                   value="{{ old('thumbnail_url', $transmision->thumbnail_url) }}">
                        </div>
                    </div>
                </div>

                <!-- Configuración y Estados -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="fas fa-toggle-on text-danger me-2"></i> Configuración de Publicación
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Switch En Vivo Ahora -->
                        <div id="enVivoContainer" class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 mb-3">
                            <div class="form-check form-switch d-flex align-items-center justify-content-between p-0">
                                <div>
                                    <label class="form-check-label fw-bold text-danger d-block mb-1" for="enVivoSwitch">
                                        <i class="fas fa-broadcast-tower me-1"></i> Transmitiendo En Vivo Ahora
                                    </label>
                                    <span class="small text-muted d-block">
                                        Solo aplica a <strong>Transmisiones En Vivo</strong>. Al activarse, enciende el botón <strong>"🔴 En Vivo"</strong> en el menú superior del sitio. Los podcasts, clips y programas grabados se publican en su sección y en la franja multimedia.
                                    </span>
                                </div>
                                <input class="form-check-input ms-3 flex-shrink-0" type="checkbox" role="switch" name="en_vivo" value="1" id="enVivoSwitch" {{ old('en_vivo', $transmision->en_vivo) ? 'checked' : '' }} style="width: 2.7em; height: 1.5em;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Switch Destacado -->
                            <div class="col-md-6">
                                <div class="form-check form-switch p-2 border rounded-3">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="destacado" value="1" id="destacadoSwitch" {{ old('destacado', $transmision->destacado) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="destacadoSwitch">
                                        <i class="fas fa-star text-warning me-1"></i> Priorizar en franja de portada
                                    </label>
                                    <span class="small text-muted d-block mt-1">
                                        Los contenidos activos se muestran bajo el navbar; los destacados aparecen primero.
                                    </span>
                                </div>
                            </div>

                            <!-- Switch Activo -->
                            <div class="col-md-6">
                                <div class="form-check form-switch p-2 border rounded-3">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="activo" value="1" id="activoSwitch" {{ old('activo', $transmision->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="activoSwitch">
                                        <i class="fas fa-check-circle text-success me-1"></i> Activo / Visible
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Guardar -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-danger btn-lg px-4 fw-bold shadow-sm flex-fill">
                        <i class="fas fa-save me-2"></i> Actualizar Transmisión
                    </button>
                    <a href="{{ route('admin.transmisiones.index') }}" class="btn btn-light btn-lg px-3 border">
                        Cancelar
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-lg px-3" onclick="if(confirm('¿Estás seguro de eliminar permanentemente esta transmisión?\nEsta acción no se puede deshacer.')) { document.getElementById('deleteTransmisionEditForm').submit(); }" title="Eliminar transmisión">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <form id="deleteTransmisionEditForm" action="{{ route('admin.transmisiones.destroy', $transmision->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

            <!-- Columna Derecha: Vista Previa Interactiva en Tiempo Real -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-3 sticky-top" style="top: 20px; z-index: 10;">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold d-flex align-items-center">
                            <i class="fas fa-play-circle text-danger me-2"></i> Vista Previa en Vivo
                        </h6>
                        <span id="previewStatusBadge" class="badge bg-success">Listo para reproducir</span>
                    </div>
                    <div class="card-body p-0 bg-black">
                        <div class="ratio ratio-16x9 position-relative" id="previewContainer">
                            <div id="previewPlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white-50 p-4 text-center d-none">
                                <i class="fas fa-video fa-3x mb-2 opacity-50"></i>
                                <p class="mb-0 small">Pega un enlace de video para previsualizar aquí</p>
                            </div>
                            <iframe id="livePreviewIframe" class="w-100 h-100" src="{{ $transmision->embed_url }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="card-body border-top bg-light">
                        <div id="previewInfo">
                            <h6 class="fw-bold text-dark mb-1" id="previewTitleDisplay">{{ $transmision->titulo }}</h6>
                            <p class="small text-muted mb-2" id="previewTypeDisplay">{{ $transmision->tipo_nombre }}</p>
                            <div class="d-flex align-items-center gap-2 small">
                                <span class="badge bg-danger" id="previewLiveBadge" style="{{ $transmision->en_vivo ? '' : 'display: none;' }}">🔴 EN VIVO</span>
                                <span class="text-muted" id="previewPlatformDisplay">
                                    <i class="{{ $transmision->plataforma_icon }}" style="color: {{ $transmision->plataforma_color }};"></i> {{ $transmision->plataforma_nombre }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('urlInput');
    const plataformaSelect = document.getElementById('plataformaSelect');
    const tipoSelect = document.getElementById('tipoSelect');
    const tituloInput = document.getElementById('tituloInput');
    const enVivoSwitch = document.getElementById('enVivoSwitch');
    const btnTestPreview = document.getElementById('btnTestPreview');

    const previewIframe = document.getElementById('livePreviewIframe');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const previewStatusBadge = document.getElementById('previewStatusBadge');
    const previewTitleDisplay = document.getElementById('previewTitleDisplay');
    const previewTypeDisplay = document.getElementById('previewTypeDisplay');
    const previewPlatformDisplay = document.getElementById('previewPlatformDisplay');
    const previewLiveBadge = document.getElementById('previewLiveBadge');

    function updatePreview() {
        const url = urlInput.value.trim();
        if (!url) {
            previewPlaceholder.classList.remove('d-none');
            previewIframe.classList.add('d-none');
            previewIframe.src = '';
            previewStatusBadge.className = 'badge bg-secondary';
            previewStatusBadge.textContent = 'Sin video cargado';
            return;
        }

        previewStatusBadge.className = 'badge bg-warning text-dark';
        previewStatusBadge.textContent = 'Cargando previsualización...';

        fetch("{{ route('admin.transmisiones.preview') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                url: url,
                plataforma: plataformaSelect.value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.embed_url) {
                previewIframe.src = data.embed_url;
                previewIframe.classList.remove('d-none');
                previewPlaceholder.classList.add('d-none');
                
                previewStatusBadge.className = 'badge bg-success';
                previewStatusBadge.textContent = 'Reproduciendo';

                if (data.detected_platform && data.detected_platform !== 'otro') {
                    plataformaSelect.value = data.detected_platform;
                }

                updateDetailsDisplay();
            } else {
                previewStatusBadge.className = 'badge bg-danger';
                previewStatusBadge.textContent = 'URL no reconocida';
            }
        })
        .catch(err => {
            console.error('Error preview:', err);
            previewStatusBadge.className = 'badge bg-danger';
            previewStatusBadge.textContent = 'Error al cargar';
        });
    }

    function updateDetailsDisplay() {
        previewTitleDisplay.textContent = tituloInput.value || 'Título de la transmisión';
        
        const tipoText = tipoSelect.options[tipoSelect.selectedIndex].text;
        previewTypeDisplay.textContent = tipoText;

        const platText = plataformaSelect.options[plataformaSelect.selectedIndex].text;
        previewPlatformDisplay.innerHTML = `<i class="fas fa-play-circle text-primary"></i> ${platText}`;

        if (enVivoSwitch.checked) {
            previewLiveBadge.style.display = 'inline-block';
        } else {
            previewLiveBadge.style.display = 'none';
        }
    }

    function syncLiveSwitchWithTipo() {
        const enVivoContainer = document.getElementById('enVivoContainer');
        if (tipoSelect.value === 'en_vivo') {
            enVivoSwitch.checked = true;
            if (enVivoContainer) enVivoContainer.classList.remove('opacity-50');
        } else {
            enVivoSwitch.checked = false;
            if (enVivoContainer) enVivoContainer.classList.add('opacity-50');
        }
    }

    btnTestPreview.addEventListener('click', updatePreview);

    let debounceTimer;
    urlInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updatePreview, 600);
    });

    tituloInput.addEventListener('input', updateDetailsDisplay);

    tipoSelect.addEventListener('change', function() {
        syncLiveSwitchWithTipo();
        updateDetailsDisplay();
    });

    enVivoSwitch.addEventListener('change', function() {
        const enVivoContainer = document.getElementById('enVivoContainer');
        if (enVivoSwitch.checked) {
            tipoSelect.value = 'en_vivo';
            if (enVivoContainer) enVivoContainer.classList.remove('opacity-50');
        } else {
            if (tipoSelect.value === 'en_vivo') {
                tipoSelect.value = 'programa';
            }
            if (enVivoContainer) enVivoContainer.classList.add('opacity-50');
        }
        updateDetailsDisplay();
    });

    plataformaSelect.addEventListener('change', updatePreview);

    // Inicializar estado del switch según tipo seleccionado
    syncLiveSwitchWithTipo();
});
</script>
@endsection

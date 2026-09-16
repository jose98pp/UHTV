@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/word-style-editor.css') }}">
<style>
    /* Estilos específicos para el editor en la vista de editar */
    #editor-container {
        min-height: 400px;
        margin-bottom: 1rem;
    }
    
    #editor-container .prose {
        font-family: inherit;
        max-width: none;
    }

    #editor-container [contenteditable] {
        min-height: 300px;
    }

    /* Asegurar que los estilos de Bootstrap no interfieran con Tailwind en el editor */
    #editor-container .p-2 {
        padding: 0.5rem !important;
    }
    
    #editor-container .p-4 {
        padding: 1rem !important;
    }

    #editor-container button {
        background: none;
        border: none;
        font-size: 14px;
    }

    #editor-container button:hover {
        background-color: #e5e7eb !important;
    }
    
    /* Responsive toolbar */
    @media (max-width: 640px) {
        #editor-container .flex-wrap {
            gap: 0.25rem;
        }
        
        #editor-container button,
        #editor-container label {
            padding: 0.375rem !important;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
    <div class="container">
        <h1 class="my-4">Editar Noticia</h1>

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display success/error messages -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.noticias.update', $noticia->id) }}" method="POST" enctype="multipart/form-data" id="noticia-form">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="titulo" class="form-label">Título: <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    name="titulo" 
                    id="titulo" 
                    class="form-control @error('titulo') is-invalid @enderror" 
                    value="{{ old('titulo', $noticia->titulo) }}" 
                    required
                    maxlength="255"
                    placeholder="Ingrese el título de la noticia">
                @error('titulo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <small class="form-text text-muted">
                        <span id="titulo-char-count">0</span> / 255 caracteres (mínimo 5)
                    </small>
                    <small id="titulo-seo-badge" class="badge bg-secondary">Muy corto</small>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="contenido" class="form-label">Contenido: <span class="text-danger">*</span></label>
                
                {{-- Include help card --}}
                @include('admin.partials.rich-text-editor-help')
                
                <input type="hidden" name="contenido" id="contenido-hidden" value="{{ old('contenido', $noticia->contenido) }}" required>
                <div id="editor-container"></div>
                <div class="d-flex flex-wrap align-items-center justify-content-between p-2 bg-light rounded border mt-2 text-muted small">
                    <div class="d-flex gap-3">
                        <span><i class="fas fa-file-word text-primary me-1"></i><strong id="content-word-count">0</strong> palabras</span>
                        <span><i class="fas fa-font text-secondary me-1"></i><strong id="content-char-count">0</strong> caracteres</span>
                    </div>
                    <div>
                        <span><i class="fas fa-stopwatch text-info me-1"></i>Lectura estimada: <strong id="content-reading-time">1 min</strong></span>
                    </div>
                </div>
                <div id="content-validation-error" class="text-danger mt-2" style="display: none;"></div>
                @if ($errors->has('contenido'))
                    <div class="text-danger mt-2">
                        {{ $errors->first('contenido') }}
                    </div>
                @endif
            </div>

            <div class="form-group mb-3">
                <label for="category_id" class="form-label">Categoría: <span class="text-danger">*</span></label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $category)
                        <option 
                            value="{{ $category->id }}" 
                            {{ old('category_id', $noticia->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="imagen" class="form-label">Imagen:</label>
                <input 
                    type="file" 
                    name="imagen" 
                    id="imagen" 
                    class="form-control" 
                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                    onchange="previewImage(event)">
                <small class="form-text text-muted">
                    Formatos permitidos: JPEG, PNG, JPG, WEBP. Tamaño máximo: 2MB. Deje vacío para mantener la imagen actual.
                </small>
                
                <!-- Current image display -->
                @if(isset($noticia->image_info) && $noticia->image_info['exists'])
                    <div id="current-image-container" class="mt-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Imagen actual</span>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Válida
                                </span>
                            </div>
                            <div class="card-body text-center">
                                <img 
                                    id="current-image-preview"
                                    src="{{ $noticia->image_info['url'] }}" 
                                    alt="Imagen actual" 
                                    style="max-width: 100%; max-height: 300px; border-radius: 8px;"
                                    onerror="this.onerror=null; this.src='{{ asset('images/default-news.svg') }}'; this.parentElement.innerHTML='<p class=\'text-danger\'>Error al cargar la imagen</p>';">
                                <div class="mt-2 text-muted small">
                                    @if($noticia->image_info['size'])
                                        <strong>Tamaño:</strong> {{ number_format($noticia->image_info['size'] / 1024, 1) }} KB<br>
                                    @endif
                                    @if($noticia->image_info['extension'])
                                        <strong>Formato:</strong> {{ strtoupper($noticia->image_info['extension']) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($noticia->imagen && !$noticia->has_valid_image)
                    <div id="current-image-container" class="mt-3">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Imagen no encontrada:</strong> {{ $noticia->imagen }}
                            <br><small>La imagen original no se encuentra en el servidor. Suba una nueva imagen.</small>
                        </div>
                    </div>
                @else
                    <div id="current-image-container" class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Esta noticia no tiene imagen asignada.
                        </div>
                    </div>
                @endif
                
                <!-- New image preview container -->
                <div id="image-preview-container" style="display: none; margin-top: 15px;">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span>Nueva imagen seleccionada</span>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="removeImagePreview()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <img 
                                id="image-preview" 
                                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3C/svg%3E" 
                                alt="Vista previa" 
                                style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            <div id="image-info" class="mt-2 text-muted small"></div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function previewImage(event) {
                    const output = document.getElementById('image-preview');
                    const container = document.getElementById('image-preview-container');
                    const info = document.getElementById('image-info');
                    const file = event.target.files[0];

                    if (file && file.type.startsWith('image/')) {
                        // Validate file size (2MB = 2048KB)
                        if (file.size > 2048 * 1024) {
                            alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                            event.target.value = '';
                            container.style.display = 'none';
                            return;
                        }

                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Tipo de archivo no permitido. Use JPEG, PNG, JPG o WEBP.');
                            event.target.value = '';
                            container.style.display = 'none';
                            return;
                        }

                        output.src = URL.createObjectURL(file);
                        container.style.display = 'block';
                        
                        // Show file information
                        const sizeKB = (file.size / 1024).toFixed(1);
                        info.innerHTML = `
                            <strong>Archivo:</strong> ${file.name}<br>
                            <strong>Tamaño:</strong> ${sizeKB} KB<br>
                            <strong>Tipo:</strong> ${file.type}<br>
                            <small class="text-info">Esta imagen reemplazará la imagen actual al guardar.</small>
                        `;
                    } else {
                        container.style.display = 'none';
                        output.src = '';
                    }
                }

                function removeImagePreview() {
                    const input = document.getElementById('imagen');
                    const container = document.getElementById('image-preview-container');
                    const output = document.getElementById('image-preview');
                    
                    input.value = '';
                    container.style.display = 'none';
                    output.src = '';
                }

                function previewGaleriaImages(event) {
                    const container = document.getElementById('galeria-preview-container');
                    container.innerHTML = '';
                    const files = event.target.files;
                    if (files && files.length > 0) {
                        container.style.display = 'flex';
                        Array.from(files).forEach((file) => {
                            if (file.type.startsWith('image/')) {
                                const col = document.createElement('div');
                                col.className = 'col-6 col-sm-4 col-md-3 position-relative';
                                const img = document.createElement('img');
                                img.src = URL.createObjectURL(file);
                                img.className = 'img-thumbnail w-100 shadow-sm rounded';
                                img.style.height = '110px';
                                img.style.objectFit = 'cover';
                                
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-dark position-absolute top-0 start-0 m-2 opacity-75';
                                badge.innerText = `${(file.size / 1024).toFixed(0)} KB`;

                                col.appendChild(img);
                                col.appendChild(badge);
                                container.appendChild(col);
                            }
                        });
                    } else {
                        container.style.display = 'none';
                    }
                }
            </script>

            <!-- Multimedia: Galería de Fotos Adicionales -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 p-3 bg-light">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-images text-primary me-2 fa-lg"></i>
                    <label for="galeria" class="form-label mb-0 fw-bold">Multimedia: Galería de Fotos Adicionales</label>
                </div>
                <p class="text-muted small mb-2">
                    Agrega más fotos a esta noticia o elimina fotos existentes de la galería.
                </p>

                @if(!empty($noticia->galeria) && is_array($noticia->galeria) && count($noticia->galeria) > 0)
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Fotos actuales en la galería (marca para eliminar):</label>
                        <div class="row g-2">
                            @foreach($noticia->galeria as $index => $fotoPath)
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="card h-100 border shadow-xs position-relative overflow-hidden">
                                        <img src="{{ asset($fotoPath) }}" alt="Foto {{ $index + 1 }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                        <div class="card-body p-2 bg-white text-center">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="checkbox" name="eliminar_galeria[]" value="{{ $fotoPath }}" id="del_foto_{{ $index }}">
                                                <label class="form-check-label text-danger small fw-semibold" for="del_foto_{{ $index }}">
                                                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <label for="galeria" class="form-label text-muted small fw-bold">Añadir más fotos a la galería:</label>
                <input 
                    type="file" 
                    name="galeria[]" 
                    id="galeria" 
                    class="form-control" 
                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                    multiple
                    onchange="previewGaleriaImages(event)">
                <small class="form-text text-muted">
                    Formatos permitidos: JPEG, PNG, JPG, WEBP. Tamaño máx: 5MB por foto.
                </small>

                <!-- Previsualización de nuevas fotos -->
                <div id="galeria-preview-container" class="row g-2 mt-2" style="display: none;"></div>
            </div>

            <div class="form-group mb-3">
                <label for="video_youtube" class="form-label">Video de YouTube (opcional):</label>
                <input 
                    type="url" 
                    name="video_youtube" 
                    id="video_youtube" 
                    class="form-control @error('video_youtube') is-invalid @enderror" 
                    value="{{ old('video_youtube', $noticia->video_youtube) }}"
                    placeholder="https://www.youtube.com/watch?v=...">
                @error('video_youtube')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <small class="form-text text-muted">Pegue la URL completa del video de YouTube.</small>
            </div>

            <div class="form-group form-check mb-3">
                <input 
                    type="checkbox" 
                    name="publicada" 
                    id="publicada" 
                    class="form-check-input" 
                    value="1" 
                    {{ $noticia->publicada ? 'checked' : '' }}>
                <label class="form-check-label" for="publicada">¿Publicar ahora?</label>
            </div>

            <div class="form-group d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                    <i class="fas fa-save me-1"></i> Actualizar Noticia
                </button>
                <button type="button" class="btn btn-info btn-lg text-white" onclick="openLivePreview()">
                    <i class="fas fa-eye me-1"></i> Vista Previa en Vivo
                </button>
                <a href="{{ route('admin.noticias.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                @if($noticia->publicada)
                    <a href="{{ $noticia->url }}" target="_blank" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-external-link-alt me-1"></i> Ver en Portal
                    </a>
                @endif
            </div>
        </form>

        @include('admin.noticias.partials.live-preview-modal')
    </div>
@push('scripts')
<!-- React libraries (carga tradicional para mayor estabilidad) -->
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>

<!-- Rich Text Editor -->
<script src="{{ asset('js/rich-text-editor.js') }}"></script>
<script src="{{ asset('js/word-style-editor.js') }}"></script>
<script src="{{ asset('js/rich-text-editor-init.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Decode HTML entities in the initial content
        let initialContent = document.getElementById('contenido-hidden').value;
        if (initialContent) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = initialContent;
            initialContent = tempDiv.innerHTML;
        }

        // Initialize Rich Text Editor with enhanced features
        window.RichTextEditorManager.initializeEditor({
            containerId: 'editor-container',
            hiddenInputId: 'contenido-hidden',
            validationErrorId: 'content-validation-error',
            initialContent: initialContent,
            required: true,
            lazyLoad: false, // Usar librerías pre-cargadas
            placeholder: 'Escriba el contenido de la noticia aquí...',
            minHeight: '300px',
            useWordStyle: true, // Usar el editor estilo Microsoft Word
            onChange: function(content) {
                console.log('Content changed, length:', content.length);
            },
            onAutoSave: function(content) {
                console.log('Auto-guardado:', new Date().toLocaleTimeString());
            }
        });

        // Enhanced form validation
        const form = document.getElementById('noticia-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate title
            const titulo = document.getElementById('titulo');
            if (titulo.value.trim().length < 5) {
                isValid = false;
                titulo.classList.add('is-invalid');
            } else {
                titulo.classList.remove('is-invalid');
            }
            
            // Validate category
            const category = document.getElementById('category_id');
            if (!category.value) {
                isValid = false;
                category.classList.add('is-invalid');
            } else {
                category.classList.remove('is-invalid');
            }
            
            // Validate content from rich text editor
            const contenidoHidden = document.getElementById('contenido-hidden');
            const textContent = contenidoHidden.value.replace(/<[^>]*>/g, '').trim();
            if (textContent.length < 30) {
                isValid = false;
                alert('El contenido debe tener al menos 30 caracteres de texto real.');
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
            });

            // Real-time counter logic
            const tituloInput = document.getElementById('titulo');
            const tituloCharCount = document.getElementById('titulo-char-count');
            const tituloSeoBadge = document.getElementById('titulo-seo-badge');

            function updateTitleStats() {
                if (!tituloInput || !tituloCharCount) return;
                const len = tituloInput.value.length;
                tituloCharCount.innerText = len;
                if (len < 10) {
                    tituloSeoBadge.className = 'badge bg-secondary';
                    tituloSeoBadge.innerText = 'Muy corto';
                } else if (len >= 10 && len < 40) {
                    tituloSeoBadge.className = 'badge bg-warning text-dark';
                    tituloSeoBadge.innerText = 'Aceptable';
                } else if (len >= 40 && len <= 70) {
                    tituloSeoBadge.className = 'badge bg-success';
                    tituloSeoBadge.innerText = 'Óptimo para Google (SEO)';
                } else {
                    tituloSeoBadge.className = 'badge bg-info text-dark';
                    tituloSeoBadge.innerText = 'Título extenso';
                }
            }

            if (tituloInput) {
                tituloInput.addEventListener('input', updateTitleStats);
                updateTitleStats();
            }

            function updateContentStats() {
                const editorEditable = document.querySelector('#editor-container [contenteditable]');
                const hiddenContent = document.getElementById('contenido-hidden');
                let text = '';
                if (editorEditable) {
                    text = editorEditable.innerText || '';
                } else if (hiddenContent) {
                    text = hiddenContent.value.replace(/<[^>]*>/g, ' ');
                }
                const cleanText = text.trim();
                const words = cleanText ? cleanText.split(/\s+/).filter(Boolean).length : 0;
                const chars = cleanText.length;
                const minutes = Math.max(1, Math.ceil(words / 200));

                const wordCountEl = document.getElementById('content-word-count');
                const charCountEl = document.getElementById('content-char-count');
                const readTimeEl = document.getElementById('content-reading-time');

                if (wordCountEl) wordCountEl.innerText = words;
                if (charCountEl) charCountEl.innerText = chars;
                if (readTimeEl) readTimeEl.innerText = `${minutes} min`;
            }

            // Check content stats periodically and on input
            setTimeout(updateContentStats, 600);
            setInterval(updateContentStats, 1000);
            document.addEventListener('input', function(e) {
                if (e.target && e.target.closest('#editor-container')) {
                    updateContentStats();
                }
            });
        });
    </script>
    @endpush
    @endsection

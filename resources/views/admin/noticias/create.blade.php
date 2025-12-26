@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rich-text-links.css') }}">
<link rel="stylesheet" href="{{ asset('css/word-style-editor.css') }}">
<style>
    /* Estilos específicos para el editor en la vista de crear */
    #editor-container {
        min-height: 400px;
        margin-bottom: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Barra de herramientas estilo Microsoft Word */
    .word-toolbar {
        background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 8px 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        min-height: 60px;
    }
    
    .word-toolbar button,
    .word-toolbar select,
    .word-toolbar label {
        background: white;
        border: 1px solid #ced4da;
        border-radius: 3px;
        padding: 6px 10px;
        font-size: 14px;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }
    
    .word-toolbar button:hover,
    .word-toolbar select:hover,
    .word-toolbar label:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .word-toolbar button:active {
        background: #dee2e6;
        transform: translateY(1px);
    }
    
    .word-toolbar .separator {
        width: 1px;
        height: 24px;
        background: #dee2e6;
        margin: 0 4px;
    }
    
    .word-toolbar .toolbar-group {
        display: flex;
        gap: 2px;
        align-items: center;
        padding: 0 4px;
        border-right: 1px solid #dee2e6;
        margin-right: 8px;
    }
    
    .word-toolbar .toolbar-group:last-child {
        border-right: none;
        margin-right: 0;
    }
    
    /* Selector de fuente estilo Word */
    .word-toolbar select {
        min-width: 120px;
        padding: 4px 8px;
        font-family: inherit;
    }
    
    /* Color picker estilo Word */
    .color-picker-container {
        position: relative;
    }
    
    .color-grid {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: grid;
        grid-template-columns: repeat(8, 20px);
        gap: 2px;
    }
    
    .color-grid button {
        width: 20px;
        height: 20px;
        border: 1px solid #dee2e6;
        border-radius: 2px;
        padding: 0;
        margin: 0;
    }
    
    .color-grid button:hover {
        border-color: #495057;
        transform: scale(1.1);
    }
    
    /* Área del editor */
    #editor-container [contenteditable] {
        min-height: 300px;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #212529;
        background: white;
    }
    
    #editor-container [contenteditable]:focus {
        outline: none;
        box-shadow: inset 0 0 0 2px #0d6efd;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .word-toolbar {
            padding: 6px 8px;
            gap: 2px;
        }
        
        .word-toolbar button,
        .word-toolbar select,
        .word-toolbar label {
            min-width: 28px;
            height: 28px;
            padding: 4px 6px;
            font-size: 12px;
        }
        
        .word-toolbar .toolbar-group {
            margin-right: 4px;
            padding: 0 2px;
        }
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
    <h1 class="my-4">Crear Noticia</h1>

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

    <form action="{{ route('admin.noticias.store') }}" method="POST" enctype="multipart/form-data" id="noticia-form">
        @csrf
        <div class="form-group mb-3">
            <label for="titulo" class="form-label">Título: <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="titulo" 
                id="titulo" 
                class="form-control @error('titulo') is-invalid @enderror" 
                value="{{ old('titulo') }}" 
                required
                maxlength="255"
                placeholder="Ingrese el título de la noticia">
            @error('titulo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <small class="form-text text-muted">Mínimo 5 caracteres, máximo 255 caracteres.</small>
        </div>

        <div class="form-group mb-3">
            <label for="contenido" class="form-label">Contenido: <span class="text-danger">*</span></label>
            
            {{-- Include help card --}}
            @include('admin.partials.rich-text-editor-help')
            
            <!-- Enlaces y Referencias - Ayuda -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-link"></i> Cómo agregar enlaces y referencias
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Para crear un enlace:</strong>
                        <ol class="mb-2">
                            <li>Selecciona el texto que quieres convertir en enlace</li>
                            <li>Haz clic en el botón <span class="badge bg-primary">🔗</span></li>
                            <li>Ingresa la URL completa (ej: https://www.ejemplo.com)</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <strong>Para quitar un enlace:</strong>
                        <ol class="mb-2">
                            <li>Selecciona el texto del enlace</li>
                            <li>Haz clic en el botón <span class="badge bg-danger">🔗❌</span></li>
                        </ol>
                    </div>
                </div>
                <hr>
                <small class="mb-0">
                    <i class="fas fa-lightbulb text-warning"></i> 
                    <strong>Tip:</strong> Los enlaces externos se abrirán automáticamente en una nueva ventana.
                </small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
            <input type="hidden" name="contenido" id="contenido-hidden" value="{{ old('contenido') }}" required>
            <div id="editor-container"></div>
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
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
            <label for="imagen" class="form-label">Imagen: <span class="text-danger">*</span></label>
            <input 
                type="file" 
                name="imagen" 
                id="imagen" 
                class="form-control" 
                accept="image/jpeg,image/png,image/jpg,image/webp" 
                onchange="previewImage(event)"
                required>
            <small class="form-text text-muted">
                Formatos permitidos: JPEG, PNG, JPG, WEBP. Tamaño máximo: 2MB.
            </small>
            
            <!-- Enhanced image preview container -->
            <div id="image-preview-container" style="display: none; margin-top: 15px;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Vista previa de la imagen</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImagePreview()">
                            <i class="fas fa-times"></i> Quitar
                        </button>
                    </div>
                    <div class="card-body text-center">
                        <img 
                            id="image-preview" 
                            src="#" 
                            alt="Vista previa" 
                            style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                        <div id="image-info" class="mt-2 text-muted small"></div>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->has('imagen'))
            <div class="alert alert-danger mt-2">
                {{ $errors->first('imagen') }}
            </div>
        @endif

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
                        <strong>Tipo:</strong> ${file.type}
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
        </script>

        <div class="form-group mb-3">
            <label for="video_youtube" class="form-label">Video de YouTube (opcional):</label>
            <input 
                type="url" 
                name="video_youtube" 
                id="video_youtube" 
                class="form-control @error('video_youtube') is-invalid @enderror" 
                value="{{ old('video_youtube') }}"
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
                value="1">
            <label class="form-check-label" for="publicada">¿Publicar ahora?</label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                <i class="fas fa-save"></i> Guardar Noticia
            </button>
            <a href="{{ route('admin.noticias.index') }}" class="btn btn-secondary btn-lg ml-2">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<!-- React libraries (carga tradicional para mayor estabilidad) -->
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>

<!-- Rich Text Editor -->
<script src="{{ asset('js/rich-text-editor.js') }}"></script>
<script src="{{ asset('js/word-style-editor.js') }}"></script>
<script src="{{ asset('js/rich-text-editor-init.js') }}"></script>

<!-- Script de diagnóstico (solo en desarrollo) -->
@if(config('app.debug'))
<script src="{{ asset('js/diagnostics.js') }}"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Rich Text Editor with enhanced features
        window.RichTextEditorManager.initializeEditor({
            containerId: 'editor-container',
            hiddenInputId: 'contenido-hidden',
            validationErrorId: 'content-validation-error',
            initialContent: document.getElementById('contenido-hidden').value,
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
            
            // Validate image
            const imagen = document.getElementById('imagen');
            if (!imagen.files.length) {
                isValid = false;
                alert('Por favor seleccione una imagen para la noticia.');
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        });
    });
</script>
@endpush

@endsection

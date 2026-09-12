<!-- Modal de Vista Previa en Vivo de la Noticia -->
<div class="modal fade" id="livePreviewModal" tabindex="-1" aria-labelledby="livePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-purple-700 via-indigo-700 to-red-600 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="p-2 bg-white/20 rounded-lg text-lg">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="livePreviewModalLabel">
                            Vista Previa en Tiempo Real
                        </h5>
                        <small class="text-white/80">Previsualización idéntica a la experiencia de usuario en el portal</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Selector de dispositivo -->
                    <div class="btn-group btn-group-sm bg-white/20 p-1 rounded-pill" role="group">
                        <button type="button" 
                                id="preview-desktop-btn" 
                                class="btn btn-sm btn-light rounded-pill px-3 fw-semibold active" 
                                onclick="setPreviewDevice('desktop')">
                            <i class="fas fa-desktop me-1"></i> Escritorio
                        </button>
                        <button type="button" 
                                id="preview-mobile-btn" 
                                class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold" 
                                onclick="setPreviewDevice('mobile')">
                            <i class="fas fa-mobile-alt me-1"></i> Móvil
                        </button>
                    </div>

                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body con simulador de portal -->
            <div class="modal-body p-0 bg-gray-100" style="min-height: 500px; max-height: 80vh;">
                <div id="preview-viewport-container" class="mx-auto transition-all duration-300 p-3 p-md-5" style="max-width: 100%;">
                    
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                        
                        <!-- Breadcrumb simulado -->
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 text-sm text-gray-500 d-flex align-items-center gap-2">
                            <span class="text-purple-600 fw-semibold"><i class="fas fa-home me-1"></i>Inicio</span>
                            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                            <span class="text-purple-600 fw-semibold" id="prev-breadcrumb-cat">Categoría</span>
                            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                            <span class="text-gray-400 text-truncate" style="max-width: 300px;" id="prev-breadcrumb-title">Título</span>
                        </div>

                        <div class="p-4 p-md-5">
                            <!-- Metadatos superiores -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-4 border-bottom border-gray-200">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-gradient-to-r from-purple-600 to-red-600 text-white px-3 py-2 rounded-pill fw-bold text-uppercase" id="prev-category-badge">
                                        General
                                    </span>
                                    <span class="text-muted small d-flex align-items-center">
                                        <i class="fas fa-clock text-purple-600 me-1"></i>
                                        <span id="prev-current-date">Hoy</span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <span class="p-2 bg-blue-100 text-blue-600 rounded-circle"><i class="fab fa-twitter"></i></span>
                                    <span class="p-2 bg-blue-100 text-blue-800 rounded-circle"><i class="fab fa-facebook"></i></span>
                                    <span class="p-2 bg-green-100 text-green-600 rounded-circle"><i class="fab fa-whatsapp"></i></span>
                                </div>
                            </div>

                            <!-- Título Principal -->
                            <h1 class="fw-bolder text-gray-900 mb-4 lh-sm" id="prev-title" style="font-size: 2.3rem;">
                                Título de la Noticia
                            </h1>

                            <!-- Autor y tiempo de lectura -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 bg-gray-50 rounded-xl mb-4 text-muted small">
                                <div class="d-flex align-items-center gap-3">
                                    <span><i class="fas fa-user-edit text-purple-600 me-1"></i>Por <strong>Redacción UHTV</strong></span>
                                    <span><i class="fas fa-eye text-purple-600 me-1"></i>1 lectura</span>
                                </div>
                                <div>
                                    <span><i class="fas fa-stopwatch text-purple-600 me-1"></i>Lectura: <strong id="prev-reading-time">1 min</strong></span>
                                </div>
                            </div>

                            <!-- Imagen Destacada -->
                            <div class="mb-4 text-center">
                                <img id="prev-featured-image" 
                                     src="/images/default-news.svg" 
                                     alt="Imagen de portada" 
                                     class="rounded-xl shadow-lg w-100 object-fit-cover" 
                                     style="max-height: 480px;">
                            </div>

                            <!-- Contenido del Artículo -->
                            <div id="prev-content-body" class="prose max-w-none text-gray-800" style="font-size: 1.15rem; line-height: 1.85;">
                                <p class="text-muted italic">El contenido ingresado se mostrará aquí...</p>
                            </div>

                            <!-- Video de YouTube simulado -->
                            <div id="prev-youtube-wrapper" class="mt-4" style="display: none;">
                                <h6 class="fw-bold text-danger mb-2"><i class="fab fa-youtube me-2"></i>Video Relacionado</h6>
                                <div class="ratio ratio-16x9 rounded-xl overflow-hidden shadow">
                                    <iframe id="prev-youtube-iframe" src="" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <div class="text-muted small">
                    <i class="fas fa-info-circle text-primary me-1"></i>
                    Esta vista previa actualiza dinámicamente el contenido del formulario sin necesidad de recargar.
                </div>
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar Vista Previa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function setPreviewDevice(device) {
        const container = document.getElementById('preview-viewport-container');
        const desktopBtn = document.getElementById('preview-desktop-btn');
        const mobileBtn = document.getElementById('preview-mobile-btn');

        if (device === 'mobile') {
            container.style.maxWidth = '420px';
            mobileBtn.classList.add('active', 'btn-light');
            mobileBtn.classList.remove('btn-outline-light');
            desktopBtn.classList.remove('active', 'btn-light');
            desktopBtn.classList.add('btn-outline-light');
        } else {
            container.style.maxWidth = '100%';
            desktopBtn.classList.add('active', 'btn-light');
            desktopBtn.classList.remove('btn-outline-light');
            mobileBtn.classList.remove('active', 'btn-light');
            mobileBtn.classList.add('btn-outline-light');
        }
    }

    function openLivePreview() {
        const titleInput = document.getElementById('titulo');
        const categorySelect = document.getElementById('category_id');
        const youtubeInput = document.getElementById('video_youtube');
        
        // Obtener contenido HTML del editor
        let contentHtml = '';
        const editorContainer = document.querySelector('#editor-container [contenteditable]');
        if (editorContainer) {
            contentHtml = editorContainer.innerHTML;
        } else {
            const hiddenContent = document.getElementById('contenido-hidden');
            if (hiddenContent) contentHtml = hiddenContent.value;
        }

        // Título
        const title = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : 'Sin título especificado';
        document.getElementById('prev-title').innerText = title;
        document.getElementById('prev-breadcrumb-title').innerText = title;

        // Categoría
        let categoryName = 'General';
        if (categorySelect && categorySelect.selectedIndex > 0) {
            categoryName = categorySelect.options[categorySelect.selectedIndex].text;
        }
        document.getElementById('prev-category-badge').innerText = categoryName;
        document.getElementById('prev-breadcrumb-cat').innerText = categoryName;

        // Fecha actual formateada en español
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('prev-current-date').innerText = now.toLocaleDateString('es-ES', options);

        // Contenido
        const contentBody = document.getElementById('prev-content-body');
        if (contentHtml && contentHtml.trim() && contentHtml !== '<p><br></p>') {
            contentBody.innerHTML = contentHtml;
        } else {
            contentBody.innerHTML = '<p class="text-muted fst-italic">No se ha redactado contenido aún para este artículo.</p>';
        }

        // Conteo de palabras y tiempo de lectura
        const plainText = contentBody.innerText || '';
        const words = plainText.trim().split(/\s+/).filter(Boolean).length;
        const readingMinutes = Math.max(1, Math.ceil(words / 200));
        document.getElementById('prev-reading-time').innerText = `${readingMinutes} min`;

        // Imagen
        const previewImg = document.getElementById('prev-featured-image');
        const filePreview = document.getElementById('image-preview');
        const existingImg = document.getElementById('current-image-preview');

        if (filePreview && filePreview.src && filePreview.src.length > 5 && !filePreview.src.endsWith('#')) {
            previewImg.src = filePreview.src;
            previewImg.style.display = 'block';
        } else if (existingImg && existingImg.src) {
            previewImg.src = existingImg.src;
            previewImg.style.display = 'block';
        } else {
            previewImg.src = '/images/default-news.svg';
            previewImg.style.display = 'block';
        }

        // YouTube
        const ytWrapper = document.getElementById('prev-youtube-wrapper');
        const ytIframe = document.getElementById('prev-youtube-iframe');
        if (youtubeInput && youtubeInput.value.trim()) {
            let ytUrl = youtubeInput.value.trim();
            let videoId = '';
            const match1 = ytUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            if (match1) videoId = match1[1];

            if (videoId) {
                ytIframe.src = `https://www.youtube.com/embed/${videoId}`;
                ytWrapper.style.display = 'block';
            } else {
                ytWrapper.style.display = 'none';
            }
        } else {
            ytWrapper.style.display = 'none';
            ytIframe.src = '';
        }

        // Mostrar Modal
        const modalEl = document.getElementById('livePreviewModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            // Fallback si modal bootstrap no está montado
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    }
</script>

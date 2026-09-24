<!-- Modal de Vista Previa en Vivo de la Noticia -->
<div class="modal fade" id="livePreviewModal" tabindex="-1" aria-labelledby="livePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
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

                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                                    <iframe id="prev-youtube-iframe" title="Video de YouTube de la noticia" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">
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

    function stopYouTubePreview() {
        const ytIframe = document.getElementById('prev-youtube-iframe');
        if (ytIframe) {
            ytIframe.removeAttribute('src');
        }
    }

    function closeLivePreview() {
        const modalEl = document.getElementById('livePreviewModal');
        if (!modalEl) return;

        // Bootstrap se encarga del backdrop, del bloqueo de scroll y de la
        // transición. No se eliminan manualmente para evitar estados intermedios.
        if (window.bootstrap && window.bootstrap.Modal) {
            const instance = window.bootstrap.Modal.getInstance(modalEl);
            if (instance) {
                instance.hide();
                return;
            }
        }

        // Fallback únicamente cuando Bootstrap no está disponible.
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        stopYouTubePreview();
    }

    function extractYouTubeId(value) {
        const rawValue = String(value || '').trim();
        if (!rawValue) return '';
        if (/^[\w-]{11}$/.test(rawValue)) return rawValue;

        try {
            const normalizedUrl = rawValue.includes('://') ? rawValue : `https://${rawValue}`;
            const parsedUrl = new URL(normalizedUrl);
            const host = parsedUrl.hostname.replace(/^www\./, '');

            if (host === 'youtu.be') {
                const candidate = parsedUrl.pathname.split('/').filter(Boolean)[0] || '';
                return /^[\w-]{11}$/.test(candidate) ? candidate : '';
            }

            if (host === 'youtube.com' || host === 'youtube-nocookie.com') {
                if (parsedUrl.pathname.startsWith('/embed/')) {
                    const candidate = parsedUrl.pathname.split('/').filter(Boolean)[1] || '';
                    return /^[\w-]{11}$/.test(candidate) ? candidate : '';
                }

                const candidate = parsedUrl.searchParams.get('v') || '';
                return /^[\w-]{11}$/.test(candidate) ? candidate : '';
            }
        } catch (error) {
            console.warn('No se pudo interpretar la URL de YouTube:', error);
        }

        return '';
    }

    function setYouTubePreview(value) {
        const wrapper = document.getElementById('prev-youtube-wrapper');
        const iframe = document.getElementById('prev-youtube-iframe');
        const videoId = extractYouTubeId(value);

        if (!wrapper || !iframe) return;

        if (videoId) {
            iframe.src = `https://www.youtube-nocookie.com/embed/${videoId}?rel=0`;
            wrapper.style.display = 'block';
        } else {
            iframe.removeAttribute('src');
            wrapper.style.display = 'none';
        }
    }

    function openLivePreview() {
        const titleInput = document.getElementById('titulo');
        const categorySelect = document.getElementById('category_id');
        const youtubeInput = document.getElementById('video_youtube');
        
        // Obtener contenido HTML sin los controles temporales de edición.
        let contentHtml = '';
        const editorContainer = document.querySelector('#editor-container [contenteditable]');
        if (editorContainer) {
            const cleanEditor = editorContainer.cloneNode(true);
            cleanEditor.querySelectorAll('.editor-image-move-handle, .editor-image-resize-handle, .editor-image-delete-handle')
                .forEach(handle => handle.remove());
            cleanEditor.querySelectorAll('.editor-image-frame').forEach(frame => {
                frame.removeAttribute('id');
                frame.removeAttribute('contenteditable');
                frame.removeAttribute('role');
                frame.removeAttribute('tabindex');
                frame.removeAttribute('aria-label');
                frame.removeAttribute('aria-selected');
                frame.removeAttribute('style');
            });
            cleanEditor.querySelectorAll('img').forEach(image => {
                image.removeAttribute('loading');
                image.removeAttribute('draggable');
                image.removeAttribute('title');
                image.removeAttribute('style');
            });
            contentHtml = cleanEditor.innerHTML;
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
        const imageInput = document.getElementById('imagen');
        const filePreview = document.getElementById('image-preview');
        const existingImg = document.getElementById('current-image-preview');

        previewImg.onerror = function() {
            this.onerror = null;
            this.src = '/images/default-news.svg';
        };

        // El input de archivo es la fuente de verdad: la imagen vacía usa un
        // data URI transparente y no debe confundirse con una selección real.
        if (imageInput && imageInput.files && imageInput.files.length > 0 && filePreview && filePreview.src) {
            previewImg.src = filePreview.src;
        } else if (existingImg && existingImg.src) {
            previewImg.src = existingImg.src;
        } else {
            previewImg.src = '/images/default-news.svg';
        }
        previewImg.style.display = 'block';

        // YouTube: acepta URL completa o el ID guardado actualmente.
        setYouTubePreview(youtubeInput ? youtubeInput.value : '');

        // Mostrar Modal
        const modalEl = document.getElementById('livePreviewModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('livePreviewModal');
        if (modalEl) {
            // Bootstrap gestiona Escape, clic exterior y eliminación del backdrop.
            modalEl.addEventListener('hidden.bs.modal', stopYouTubePreview);
        }
    });
</script>

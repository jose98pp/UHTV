@php
    $transmisionesRecientes = $transmisionesRecientes ?? collect();
    $transmisionEnVivo = $transmisionEnVivo ?? null;
    $modalInitialStream = $transmisionEnVivo ?? ($transmisionesRecientes->first() ?? null);
@endphp

<!-- Modal de Transmisión En Vivo y Streaming UHTV -->
<div id="liveStreamModal" 
     class="fixed inset-0 z-[99999] hidden opacity-0 transition-opacity duration-300 overflow-y-auto bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-5"
     aria-labelledby="modal-live-title" 
     role="dialog" 
     aria-modal="true">

    <div class="relative w-full max-w-4xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden transform scale-95 transition-all duration-300 text-white" id="liveStreamModalContent">
        
        <!-- Header del Modal -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-gradient-to-r from-gray-900 via-gray-850 to-gray-900">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-500">
                    <i class="fas fa-satellite-dish text-base {{ ($transmisionEnVivo) ? 'animate-pulse' : '' }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base sm:text-lg font-bold text-white tracking-tight" id="modal-live-title">
                            UHTV Play
                        </h3>
                        @if($transmisionEnVivo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-red-600 text-white shadow-sm animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                En Vivo Ahora
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-700/80 text-gray-300">
                                <i class="fas fa-history text-[9px]"></i> Últimas Emisiones
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 truncate max-w-md hidden sm:block">Transmisión en directo, podcasts y clips informativos</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('transmisiones.en-vivo') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 text-white transition-all duration-200">
                    <i class="fas fa-expand text-[11px]"></i>
                    <span>Pantalla Completa</span>
                </a>
                <button type="button" 
                        id="btnCloseLiveModal" 
                        class="w-9 h-9 rounded-full bg-white/5 hover:bg-red-600/80 hover:text-white text-gray-400 flex items-center justify-center transition-all duration-200 focus:outline-none" 
                        aria-label="Cerrar reproductor">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Video Player Body -->
        <div class="relative bg-black w-full" style="padding-top: 56.25%;">
            @if($modalInitialStream && !empty($modalInitialStream->embed_url))
                <iframe id="liveModalIframe" 
                        src="" 
                        data-initial-src="{{ $modalInitialStream->embed_url }}"
                        title="{{ $modalInitialStream->titulo }}" 
                        class="absolute inset-0 w-full h-full border-0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                </iframe>
            @else
                <div id="liveModalEmpty" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-gray-400 bg-gray-950">
                    <i class="fas fa-tv fa-3x mb-3 text-gray-600"></i>
                    <h4 class="text-white font-bold text-base mb-1">No hay transmisiones activas en este momento</h4>
                    <p class="text-xs max-w-sm text-gray-400 mb-4">Sintoniza nuestras próximas transmisiones o revisa nuestras noticias de última hora.</p>
                    <a href="{{ route('transmisiones.en-vivo') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Explorar Archivo de Videos y Podcasts
                    </a>
                </div>
            @endif
        </div>

        <!-- Info & Switcher Bar -->
        <div class="p-4 sm:p-5 bg-gray-900 border-t border-white/5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span id="modalCurrentPlatformBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase tracking-wider" 
                              style="background-color: {{ $modalInitialStream->plataforma_color ?? '#ff0000' }};">
                            <i id="modalCurrentPlatformIcon" class="{{ $modalInitialStream->plataforma_icon ?? 'fab fa-youtube' }}"></i>
                            <span id="modalCurrentPlatformText">{{ $modalInitialStream->plataforma_nombre ?? 'YouTube' }}</span>
                        </span>
                        <span id="modalCurrentTypeBadge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-white/10 text-gray-200">
                            {{ $modalInitialStream->tipo_nombre ?? 'Transmisión' }}
                        </span>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-white line-clamp-1" id="modalCurrentTitle">
                        {{ $modalInitialStream->titulo ?? 'Última Hora TV - Transmisión' }}
                    </h4>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" 
                            id="btnShareLiveStream" 
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 text-white flex items-center gap-1.5 transition-all">
                        <i class="fas fa-share-alt"></i>
                        <span>Compartir</span>
                    </button>
                    <a href="{{ route('transmisiones.en-vivo') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-red-600 to-purple-600 hover:from-red-500 hover:to-purple-500 text-white flex items-center gap-1.5 transition-all shadow-md">
                        <span>Ver Más</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Lista de videos / emisiones recientes para alternar -->
            @if(isset($transmisionesRecientes) && $transmisionesRecientes->count() > 1)
                <div class="pt-3 border-t border-white/10">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fas fa-layer-group text-purple-400"></i> Otras Transmisiones, Podcasts y Clips:
                    </p>
                    <div class="flex space-x-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
                        @foreach($transmisionesRecientes as $item)
                            <button type="button" 
                                    class="modal-stream-item flex-shrink-0 w-44 text-left p-2 rounded-xl bg-gray-800/80 hover:bg-gray-750 border border-white/5 hover:border-purple-500/40 transition-all duration-200 group focus:outline-none"
                                    data-embed="{{ $item->embed_url }}"
                                    data-title="{{ $item->titulo }}"
                                    data-platform="{{ $item->plataforma_nombre }}"
                                    data-platform-color="{{ $item->plataforma_color }}"
                                    data-platform-icon="{{ $item->plataforma_icon }}"
                                    data-type="{{ $item->tipo_nombre }}"
                                    data-url="{{ route('transmisiones.en-vivo') }}">
                                <div class="relative w-full h-20 rounded-lg overflow-hidden mb-1.5 bg-black">
                                    <img src="{{ $item->effective_thumbnail }}" alt="{{ $item->titulo }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" onerror="this.src='/images/Logo.jpg'">
                                    @if($item->en_vivo)
                                        <span class="absolute top-1 left-1 bg-red-600 text-white text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded shadow">
                                            VIVO
                                        </span>
                                    @endif
                                    <div class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-white" style="background-color: {{ $item->plataforma_color }};">
                                        <i class="{{ $item->plataforma_icon }}"></i>
                                    </div>
                                </div>
                                <h5 class="text-xs font-semibold text-gray-200 group-hover:text-purple-400 line-clamp-1 transition-colors">
                                    {{ $item->titulo }}
                                </h5>
                                <p class="text-[10px] text-gray-400 capitalize">{{ $item->tipo_nombre }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script del Modal de Transmisiones -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('liveStreamModal');
    const modalContent = document.getElementById('liveStreamModalContent');
    const iframe = document.getElementById('liveModalIframe');
    const closeBtn = document.getElementById('btnCloseLiveModal');
    const openBtns = document.querySelectorAll('[data-open-live-modal]');

    const titleEl = document.getElementById('modalCurrentTitle');
    const platBadge = document.getElementById('modalCurrentPlatformBadge');
    const platIcon = document.getElementById('modalCurrentPlatformIcon');
    const platText = document.getElementById('modalCurrentPlatformText');
    const typeBadge = document.getElementById('modalCurrentTypeBadge');
    const shareBtn = document.getElementById('btnShareLiveStream');

    function openLiveModal(embedUrl, title, platform, platformColor, platformIcon, type) {
        if (!modal) return;

        if (embedUrl && iframe) {
            iframe.src = embedUrl;
        } else if (iframe && iframe.getAttribute('data-initial-src')) {
            iframe.src = iframe.getAttribute('data-initial-src');
        }

        if (title && titleEl) titleEl.textContent = title;
        if (platform && platText) platText.textContent = platform;
        if (platformColor && platBadge) platBadge.style.backgroundColor = platformColor;
        if (platformIcon && platIcon) platIcon.className = platformIcon;
        if (type && typeBadge) typeBadge.textContent = type;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Animación suave de entrada
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeLiveModal() {
        if (!modal) return;

        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            if (iframe) {
                iframe.src = ''; // Detener reproducción al cerrar
            }
        }, 300);
    }

    // Delegación de eventos para asegurar apertura del modal desde cualquier elemento o hijo
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-open-live-modal]');
        if (trigger) {
            e.preventDefault();
            const embed = trigger.getAttribute('data-stream-embed');
            const title = trigger.getAttribute('data-stream-title');
            const platform = trigger.getAttribute('data-stream-platform');
            const platformColor = trigger.getAttribute('data-stream-platform-color');
            const platformIcon = trigger.getAttribute('data-stream-platform-icon');
            const type = trigger.getAttribute('data-stream-type');
            openLiveModal(embed, title, platform, platformColor, platformIcon, type);
        }
    });

    // Cerrar botón y clic fuera
    if (closeBtn) closeBtn.addEventListener('click', closeLiveModal);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLiveModal();
            }
        });
    }

    // Tecla Escape para cerrar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeLiveModal();
        }
    });

    // Cambiar de stream al hacer clic en un elemento de la lista
    const streamItems = document.querySelectorAll('.modal-stream-item');
    streamItems.forEach(item => {
        item.addEventListener('click', function() {
            const embed = this.getAttribute('data-embed');
            const title = this.getAttribute('data-title');
            const platform = this.getAttribute('data-platform');
            const platformColor = this.getAttribute('data-platform-color');
            const platformIcon = this.getAttribute('data-platform-icon');
            const type = this.getAttribute('data-type');

            if (iframe && embed) {
                iframe.src = embed;
            }
            if (title && titleEl) titleEl.textContent = title;
            if (platform && platText) platText.textContent = platform;
            if (platformColor && platBadge) platBadge.style.backgroundColor = platformColor;
            if (platformIcon && platIcon) platIcon.className = platformIcon;
            if (type && typeBadge) typeBadge.textContent = type;
        });
    });

    // Botón de compartir
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            const url = window.location.origin + '/en-vivo';
            const title = titleEl ? titleEl.textContent : 'Transmisión en Vivo UHTV';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: 'Mira esta transmisión en Última Hora TV',
                    url: url
                }).catch(err => console.log('Share canceled'));
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = shareBtn.innerHTML;
                    shareBtn.innerHTML = '<i class="fas fa-check text-green-400"></i><span>¡Enlace copiado!</span>';
                    setTimeout(() => {
                        shareBtn.innerHTML = originalText;
                    }, 2500);
                });
            }
        });
    }

    // Exponer globalmente para abrir desde cualquier script
    window.openUHTVLiveModal = openLiveModal;
});
</script>
